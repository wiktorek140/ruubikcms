<?php

namespace Ruubik\Tests\Service;

use PHPUnit\Framework\TestCase;
use Ruubik\Service\Db\Database;
use Ruubik\Service\Db\SQLite;
use PDO;
use PDOStatement;

class DatabaseHelperTest extends TestCase
{
    private PDO $mockPdo;
    private Database $dbHelper;

    protected function setUp(): void
    {
        $this->mockPdo = $this->createMock(PDO::class);
        $this->dbHelper = new SQLite($this->mockPdo);
    }

    public function testQuerySingle()
    {
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->method('fetchColumn')->willReturn('TestValue');

        $this->mockPdo
            ->method('query')
            ->with('SELECT 1')
            ->willReturn($mockStatement);

        $result = $this->dbHelper->query('SELECT 1');
        $this->assertEquals('TestValue', $result);
    }

    public function testQueryPrepared()
    {
        $mockStatement = $this->createMock(PDOStatement::class);
        $mockStatement->method('execute')->willReturn(true);
        $mockStatement->method('fetch')->willReturn(['TestValue']);

        $this->mockPdo
            ->method('prepare')
            ->with('SELECT ?')
            ->willReturn($mockStatement);

        $result = $this->dbHelper->query('SELECT ?', ['param']);
        $this->assertEquals('TestValue', $result);
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

        $this->mockPdo
            ->method('prepare')
            ->willReturn($mockStatement);

        $result = $this->dbHelper->queryAll('SELECT * FROM table');
        $this->assertCount(2, $result);
    }
}
