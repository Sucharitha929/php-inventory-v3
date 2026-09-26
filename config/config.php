<?php
declare(strict_types=1);

return [
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_name' => getenv('DB_NAME') ?: 'inventory_db',
    'db_user' => getenv('DB_USER') ?: 'bala',
    'db_password' => getenv('DB_PASSWORD') ?: 'admin_12',
    'app_name' => getenv('APP_NAME') ?: 'K-15 Inventory',
];
