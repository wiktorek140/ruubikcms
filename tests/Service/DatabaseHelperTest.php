<?php

namespace Ruubik\Tests\Service;

use PHPUnit\Framework\TestCase;
use Ruubik\Service\Db\Database;
use Ruubik\Service\Db\SQLite;
use PDO;
use PDOStatement;

class DatabaseHelperTest extends TestCase
{
    //private PDO $mockPdo;
    //private Database $dbHelper;

    protected function setUp(): void
    {
        //$mockPdo = $this->createMock(PDO::class);
        //$this->dbHelper = new SQLite($this->mockPdo);
    }

    public function testQuerySingle()
    {
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->method('fetch')->willReturn(['col' => 'TestValue']);
        $mockStatement->method('execute')->willReturn(true);

$mockPdo = $this->createMock(PDO::class);
$mockPdo
            ->method('prepare')
            ->with('SELECT 1')
            ->willReturn($mockStatement);
$mockPdo
            ->method('query')
            ->with('SELECT 1')
            ->willReturn($mockStatement);

        $dbHelper = new SQLite($mockPdo);

        $result = $dbHelper->query('SELECT 1');
        $this->assertIsArray($result);
        $this->assertEquals('TestValue', $result['col']);
    }

    public function testQueryPrepared()
    {
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->method('execute')->willReturn(true);
        $mockStatement->method('fetch')->willReturn(['col'=>'TestValue']);

        $mockPdo = $this->createMock(PDO::class);
        $mockPdo
            ->method('prepare')
            ->with('SELECT ?')
            ->willReturn($mockStatement);
        $dbHelper = new SQLite($mockPdo);
        
        $result = $dbHelper->query('SELECT ?', ['param']);
        $this->assertIsArray($result);
        $this->assertEquals('TestValue', $result['col']);
    }

    public function testQueryAll()
    {
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->method('execute')->willReturn(true);
        $mockStatement->method('fetchAll')->willReturn([
            [
                'id'   => 1,
                'name' => 'Test',
            ],
            [
                'id'   => 2,
                'name' => 'Example',
            ],
        ]);

        $mockPdo = $this->createMock(PDO::class);
        $mockPdo
            ->method('prepare')
            ->willReturn($mockStatement);

        $dbHelper = new SQLite($mockPdo);

        $result = $dbHelper->queryAll('SELECT * FROM table');
        $this->assertCount(2, $result);
    }
}
