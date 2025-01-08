<?php

declare(strict_types=1);

namespace Ruubik\Service\Db;

use PDO;

class SQLite implements Database
{
    public const PDO_DB_FOLDER = 'sqlite';
    public const PDO_DB_DRIVER = 'sqlite';
    public const PDO_DB_NAME = 'ruubikcms.sqlite';

    private PDO $dbh;

    public function __construct()
    {
        $this->dbh = new PDO(self::PDO_DB_DRIVER . ':' . self::PDO_DB_FOLDER . '/' . self::PDO_DB_NAME);
    }

    public function query(string $query, ?array $bindParams = null, ?string $order = null)
    {
        // TODO: Implement query() method.
    }

    public function execute(string $query, ?array $bindParams = null): void
    {
        $stmt = $this->dbh->prepare($query);
        foreach ($bindParams as $key => $value) {
            $stmt->bindParam($key, $value);
        }

        $stmt->execute();
    }
}
