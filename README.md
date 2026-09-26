# PHP Inventory Management – Complete CI/CD Project

A production-style PHP inventory application with a Jenkins CI/CD pipeline covering:

**Checkout → Compile/Lint → Build → Unit Test → Package → Install → Docker Build → Deploy → Smoke Test**

The application is based on the uploaded legacy PHP inventory/login flow: session login, inventory dashboard, add/receive/issue/search/stock/terminate/demand modules. The original files use deprecated `mysql_*` calls and are therefore represented here with PDO and prepared statements for a runnable modern PHP implementation. The uploaded dashboard shows the K-15 Inventory modules and login flow. 

## Stack
- PHP 8.3
- SQLite for zero-dependency local/demo database
- PDO
- PHPUnit 11
- Composer
- Apache + PHP 8.3 Docker image
- Jenkins Declarative Pipeline
- Docker Compose

## Quick start

```bash
composer install
php scripts/init-db.php
php -S 0.0.0.0:8080 -t app/public
```

Open `http://localhost:8080`.

Demo credentials:
- Username: `admin`
- Password: `admin123`

## CI/CD pipeline

Jenkins requires Docker access on the agent. Configure a Pipeline job pointing to this repository and use the included `Jenkinsfile`.

Pipeline stages:
1. Checkout
2. Install Dependencies
3. Compile / PHP Syntax Validation
4. Build
5. Unit Tests
6. Package Artifact
7. Install Package
8. Docker Build
9. Deploy
10. Smoke Test

For a local pipeline simulation:

```bash
bash scripts/ci.sh
```

For container deployment:

```bash
docker compose up -d --build
curl -f http://localhost:8080/health.php
```

## Project structure

```text
php-inventory-devops/
├── app/
│   ├── public/              # Web root
│   ├── src/                 # Application classes
│   └── tests/               # PHPUnit tests
├── config/                  # Runtime configuration
├── docker/                  # Docker/Apache configuration
├── scripts/                 # Build, test, package and deployment scripts
├── composer.json
├── Dockerfile
├── docker-compose.yml
├── Jenkinsfile
└── README.md
```
