pipeline {

    agent any

    environment {

        // Application
        APP_NAME = 'php-inventory'
        K8S_NAMESPACE = 'inventory'

        // Docker Hub
        DOCKERHUB_REPO = 'balakrishnasetlem/php-inventory'
        IMAGE_TAG = "${BUILD_NUMBER}"
        DOCKER_IMAGE = "${DOCKERHUB_REPO}:${IMAGE_TAG}"

        // Helm chart
        HELM_RELEASE = 'php-inventory'
        HELM_CHART = './php-inventory-helm'

        // Kubernetes
        K8S_DEPLOYMENT = 'php-inventory'
        K8S_CONTAINER = 'php-inventory'
    }

    stages {

        // =========================================================
        // 1. CHECKOUT
        // =========================================================

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        // =========================================================
        // 2. PHP DEPENDENCIES
        // =========================================================

        stage('Install Dependencies') {
            steps {
                sh '''
                    set -e

                    composer install \
                        --no-interaction \
                        --prefer-dist
                '''
            }
        }

        // =========================================================
        // 3. PHP LINT
        // =========================================================

        stage('PHP Lint') {
            steps {
                sh '''
                    set -e

                    find app -name "*.php" -print0 |
                    xargs -0 -n1 php -l
                '''
            }
        }

        // =========================================================
        // 4. UNIT TEST
        // =========================================================

        stage('Unit Test') {
            steps {
                sh '''
                    set -e

                    if [ -x vendor/bin/phpunit ]; then

                        vendor/bin/phpunit \
                            --testdox \
                            --log-junit test-results.xml

                    else

                        echo "ERROR: PHPUnit is not installed"
                        exit 1

                    fi
                '''
            }

            post {
                always {
                    junit allowEmptyResults: true,
                          testResults: 'test-results.xml'
                }
            }
        }

        // =========================================================
        // 5. BUILD APPLICATION
        // =========================================================

        stage('Build') {
            steps {
                sh '''
                    set -e

                    bash scripts/build.sh
                '''
            }
        }

        // =========================================================
        // 6. PACKAGE APPLICATION
        // =========================================================

        stage('Package') {
            steps {
                sh '''
                    set -e

                    BUILD_VERSION=${BUILD_NUMBER} \
                    bash scripts/package.sh
                '''

                archiveArtifacts(
                    artifacts: 'dist/*.tar.gz',
                    fingerprint: true
                )
            }
        }

        // =========================================================
        // 7. VERIFY PACKAGE
        // =========================================================

        stage('Install Package') {
            steps {
                sh '''
                    set -e

                    rm -rf workspace-install
                    mkdir -p workspace-install

                    tar -xzf dist/*.tar.gz \
                        -C workspace-install

                    php -l workspace-install/app/public/index.php
                '''
            }
        }

        // =========================================================
        // 8. DOCKER BUILD
        // =========================================================

        stage('Docker Build') {
            steps {
                sh '''
                    set -e

                    docker build \
                        -t ${DOCKER_IMAGE} \
                        -t ${DOCKERHUB_REPO}:latest \
                        .
                '''
            }
        }

        // =========================================================
        // 9. DOCKER LOGIN
        // =========================================================

        stage('Docker Login') {
            steps {

                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub-credentials',
                        usernameVariable: 'DOCKER_USERNAME',
                        passwordVariable: 'DOCKER_PASSWORD'
                    )
                ]) {

                    sh '''
                        set -e

                        echo "$DOCKER_PASSWORD" |
                        docker login \
                            -u "$DOCKER_USERNAME" \
                            --password-stdin
                    '''
                }
            }
        }

        // =========================================================
        // 10. PUSH DOCKER IMAGE
        // =========================================================

        stage('Push Docker Image') {
            steps {
                sh '''
                    set -e

                    docker push ${DOCKER_IMAGE}

                    docker push ${DOCKERHUB_REPO}:latest

                    docker logout
                '''
            }
        }

        // =========================================================
        // 11. VERIFY KUBERNETES ACCESS
        // =========================================================

        stage('Verify Kubernetes') {
            steps {
                sh '''
                    set -e

                    kubectl cluster-info

                    kubectl get nodes

                    kubectl get namespace ${K8S_NAMESPACE}
                '''
            }
        }

        // =========================================================
        // 12. HELM LINT
        // =========================================================

        stage('Helm Lint') {
            steps {
                sh '''
                    set -e

                    helm lint ${HELM_CHART}
                '''
            }
        }

        // =========================================================
        // 13. DEPLOY USING HELM
        // =========================================================

        stage('Deploy Kubernetes') {
            steps {
                sh '''
                    set -e

                    helm upgrade --install ${HELM_RELEASE} \
                        ${HELM_CHART} \
                        --namespace ${K8S_NAMESPACE} \
                        --create-namespace \
                        --set image.repository=${DOCKERHUB_REPO} \
                        --set image.tag=${IMAGE_TAG} \
                        --wait \
                        --timeout 5m
                '''
            }
        }

        // =========================================================
        // 14. ROLLOUT STATUS
        // =========================================================

        stage('Rollout') {
            steps {
                sh '''
                    set -e

                    kubectl rollout status \
                        deployment/${K8S_DEPLOYMENT} \
                        -n ${K8S_NAMESPACE} \
                        --timeout=300s
                '''
            }
        }

        // =========================================================
        // 15. VERIFY PODS
        // =========================================================

        stage('Verify Pods') {
            steps {
                sh '''
                    set -e

                    echo "================ PODS ================"

                    kubectl get pods \
                        -n ${K8S_NAMESPACE} \
                        -o wide

                    echo "================ DEPLOYMENT ================"

                    kubectl get deployment \
                        ${K8S_DEPLOYMENT} \
                        -n ${K8S_NAMESPACE}

                    echo "================ SERVICE ================"

                    kubectl get svc \
                        -n ${K8S_NAMESPACE}
                '''
            }
        }

        // =========================================================
        // 16. VERIFY LOAD BALANCER
        // =========================================================

        stage('Verify Load Balancer') {
            steps {
                sh '''
                    set -e

                    echo "================ SERVICE DETAILS ================"

                    kubectl describe service \
                        ${APP_NAME} \
                        -n ${K8S_NAMESPACE}

                    echo "================ LOAD BALANCER ADDRESS ================"

                    kubectl get service \
                        ${APP_NAME} \
                        -n ${K8S_NAMESPACE} \
                        -o wide
                '''
            }
        }
    }

    // =============================================================
    // POST ACTIONS
    // =============================================================

    post {

        success {
            echo '''
            ==========================================
            DEPLOYMENT SUCCESSFUL
            ==========================================
            '''
        }

        failure {
            echo '''
            ==========================================
            DEPLOYMENT FAILED
            Check the Jenkins console output.
            ==========================================
            '''
        }

        always {
            echo "Jenkins Build Number: ${BUILD_NUMBER}"
            echo "Docker Image: ${DOCKER_IMAGE}"
            echo "Kubernetes Namespace: ${K8S_NAMESPACE}"
        }
    }
}
