<?php
declare(strict_types=1);
namespace InventoryTests;
use Inventory\Auth;
use PHPUnit\Framework\TestCase;
use PDO;
final class AuthTest extends TestCase
{
    public function testValidCredentialsAuthenticate(): void
    {
        $db=new PDO('sqlite::memory:');
        $db->exec('CREATE TABLE users (username TEXT, password_hash TEXT)');
        $stmt=$db->prepare('INSERT INTO users VALUES(?,?)');
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
        $this->assertTrue((new Auth($db))->authenticate('admin','admin123'));
    }
    public function testInvalidPasswordFails(): void
    {
        $db=new PDO('sqlite::memory:');
        $db->exec('CREATE TABLE users (username TEXT, password_hash TEXT)');
        $stmt=$db->prepare('INSERT INTO users VALUES(?,?)');
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
        $this->assertFalse((new Auth($db))->authenticate('admin','wrong'));
    }
}
