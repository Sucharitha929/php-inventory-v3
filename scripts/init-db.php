<?php
declare(strict_types=1);
$config = require __DIR__ . '/../config/config.php';
$storage = dirname($config['db']);
if (!is_dir($storage)) mkdir($storage, 0775, true);
$db = new PDO('sqlite:' . $config['db']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT UNIQUE NOT NULL, password_hash TEXT NOT NULL)');
$db->exec('CREATE TABLE IF NOT EXISTS items (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, quantity INTEGER NOT NULL DEFAULT 0)');
$stmt=$db->prepare('INSERT OR IGNORE INTO users(username,password_hash) VALUES(?,?)');
$stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
$seed=$db->prepare('INSERT INTO items(name,quantity) VALUES(?,?)');
foreach ([['Laptop',10],['Keyboard',25],['Monitor',8]] as $item) $seed->execute($item);
echo "Database initialized.\n";
