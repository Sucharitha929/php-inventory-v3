<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

use Inventory\Auth;
use Inventory\Inventory;

session_start();
$auth = new Auth($db);
$inventory = new Inventory($db);
$error = '';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($auth->authenticate((string)$_POST['username'], (string)$_POST['password'])) {
        $_SESSION['logged_in'] = true;
        header('Location: /'); exit;
    }
    $error = 'Username and password do not match.';
}

if (!($_SESSION['logged_in'] ?? false)):
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Login - K-15 Inventory</title><style>body{font-family:Arial;background:#111827;color:#fff;display:grid;place-items:center;height:100vh}.card{background:#fff;color:#111827;padding:32px;border-radius:12px;width:320px}input,button{width:100%;padding:11px;margin:7px 0;box-sizing:border-box}button{background:#2563eb;color:#fff;border:0;border-radius:6px}.error{color:#dc2626}</style></head><body><div class="card"><h2>Inventory Login</h2><?php if($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?><form method="post"><input name="username" placeholder="Username" required><input name="password" type="password" placeholder="Password" required><button name="login">Sign in</button></form><small>Demo: admin / admin123</small></div></body></html><?php exit; endif;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $id = (int)($_POST['id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 0);

        switch ($_POST['action']) {
            case 'add':
                $inventory->add((string)$_POST['name'], $qty);
                break;

            case 'receive':
                $inventory->receive($id, $qty);
                break;

            case 'issue':
                $inventory->issue($id, $qty);
                break;
        }

        // Store message in session before redirect
        $_SESSION['message'] = 'Transaction completed successfully.';

    } catch (Throwable $e) {
        $_SESSION['message'] = $e->getMessage();
    }

    // POST → Redirect → GET
    header('Location: /');
    exit;
}

// Get message after redirect
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

$items = $inventory->all();
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>K-15 Inventory</title><style>body{font-family:Arial;margin:0;background:#f3f4f6;color:#111827}.nav{background:#111827;color:white;padding:16px 6%;display:flex;justify-content:space-between}.container{max-width:1100px;margin:30px auto;padding:0 20px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px}.card{background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 10px #ddd}input,button{padding:9px;margin:4px}button{border:0;border-radius:5px;background:#2563eb;color:#fff}.danger{background:#dc2626}.ok{background:#166534}.msg{padding:12px;background:#e0f2fe;border-radius:6px}</style></head><body><div class="nav"><b>K-15 INVENTORY</b><a href="?logout=1" style="color:white">Logout</a></div><div class="container"><h1>Inventory Dashboard</h1><?php if($message): ?><p class="msg"><?=htmlspecialchars($message)?></p><?php endif; ?><div class="grid"><div class="card"><h3>Add Item</h3><form method="post"><input type="hidden" name="action" value="add"><input name="name" placeholder="Item name" required><input name="quantity" type="number" min="1" placeholder="Qty" required><button>Add</button></form></div><div class="card"><h3>Stock</h3><strong><?=array_sum(array_column($items,'quantity'))?></strong> units in <?=count($items)?> items</div></div><h2>Items</h2><div class="card"><table width="100%" cellpadding="8"><tr><th>ID</th><th>Item</th><th>Stock</th><th>Actions</th></tr><?php foreach($items as $item): ?><tr><td><?=$item['id']?></td><td><?=htmlspecialchars($item['name'])?></td><td><?=$item['quantity']?></td><td><form method="post" style="display:inline"><input type="hidden" name="action" value="receive"><input type="hidden" name="id" value="<?=$item['id']?>"><input name="quantity" type="number" min="1" value="1" style="width:60px"><button class="ok">Receive</button></form><form method="post" style="display:inline"><input type="hidden" name="action" value="issue"><input type="hidden" name="id" value="<?=$item['id']?>"><input name="quantity" type="number" min="1" value="1" style="width:60px"><button class="danger">Issue</button></form></td></tr><?php endforeach; ?></table></div></div></body></html>
