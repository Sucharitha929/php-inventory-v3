<?php
declare(strict_types=1);

$config = require __DIR__ . '/../../config/config.php';

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=utf8mb4',
    $config['db_host'],
    $config['db_name']
);

$db = new PDO(
    $dsn,
    $config['db_user'],
    $config['db_password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // AWS RDS TLS/SSL
        PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/rds/global-bundle.pem', 
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,

    ]
);

require_once __DIR__ . '/../../vendor/autoload.php';
