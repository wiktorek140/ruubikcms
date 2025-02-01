<?php

namespace Ruubik\Tests\Service\DI;

use Ruubik\Service\Db\Database;

class TestDatabase implements Database
{
    public function query(string $query, ?array $bindParams = null, ?string $order = null): mixed
    {
        return [];
    }

    public function execute(string $query, ?array $bindParams = null): void
    {
    }

    public function queryAll(string $query, array $params = []): array
    {
        return [];
    }
}
