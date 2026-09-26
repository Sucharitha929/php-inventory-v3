<?php
declare(strict_types=1);

namespace Inventory;

use PDO;

final class Auth
{
    public function __construct(private PDO $db) {}

    public function authenticate(string $username, string $password): bool
    {
        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $hash = $stmt->fetchColumn();
        return is_string($hash) && password_verify($password, $hash);
    }
}
