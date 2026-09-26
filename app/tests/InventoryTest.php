<?php
declare(strict_types=1);
namespace InventoryTests;
use Inventory\Inventory;
use PHPUnit\Framework\TestCase;
use PDO;
final class InventoryTest extends TestCase
{
    private function inventory(): Inventory
    {
        $db=new PDO('sqlite::memory:'); $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $db->exec('CREATE TABLE items(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT,quantity INTEGER)');
        $db->exec("INSERT INTO items(name,quantity) VALUES('Mouse',10)");
        return new Inventory($db);
    }
    public function testAddReceiveAndIssue(): void
    {
        $i=$this->inventory(); $i->add('Keyboard',5); $items=$i->all();
        $this->assertCount(2,$items);
        $id=(int)$items[0]['id']; $i->receive($id,3); $i->issue($id,2);
        $this->assertSame(6,(int)$i->all()[0]['quantity']);
    }
}
