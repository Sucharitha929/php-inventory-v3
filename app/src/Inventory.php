<?php
declare(strict_types=1);

namespace Inventory;

use PDO;
use RuntimeException;

final class Inventory
{
    public function __construct(private PDO $db) {}

    public function add(string $name, int $quantity): void
    {
        if ($quantity < 1 || trim($name) === '') {
            throw new RuntimeException('Invalid item');
        }
        $stmt = $this->db->prepare('INSERT INTO items(name, quantity) VALUES(:name, :quantity)');
        $stmt->execute(['name' => trim($name), 'quantity' => $quantity]);
    }

    public function issue(int $id, int $quantity): void
    {
        $stmt = $this->db->prepare('UPDATE items SET quantity = quantity - :qty WHERE id = :id AND quantity >= :qty');
        $stmt->execute(['qty' => $quantity, 'id' => $id]);
        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('Insufficient stock or item not found');
        }
    }

    public function receive(int $id, int $quantity): void
    {
        $stmt = $this->db->prepare('UPDATE items SET quantity = quantity + :qty WHERE id = :id');
        $stmt->execute(['qty' => $quantity, 'id' => $id]);
        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('Item not found');
        }
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, name, quantity FROM items ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    }
}
