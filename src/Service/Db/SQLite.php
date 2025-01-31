<?php

declare(strict_types=1);

namespace Ruubik\Service\Db;

use PDO;

class SQLite implements Database
{
    public const PDO_DB_FOLDER = 'sqlite';
    public const PDO_DB_DRIVER = 'sqlite';
    public const PDO_DB_NAME = 'ruubikcms.sqlite';

    private readonly PDO $dbh;

    public function __construct()
    {
        $this->dbh = new PDO(self::PDO_DB_DRIVER . ':' . self::PDO_DB_FOLDER . '/' . self::PDO_DB_NAME);
    }

    public function query(string $query, ?array $bindParams = null, ?string $order = null)
    {
        $stmt = $this->dbh->prepare($query);
        if ($stmt->execute($bindParams)) {
            $result = $stmt->fetch(PDO::FETCH_NUM);
            return $result[0] ?? null;
        }

        return null;
    }

    public function execute(string $query, ?array $bindParams = null): void
    {
        $stmt = $this->dbh->prepare($query);
        foreach ($bindParams as $key => $value) {
            $stmt->bindParam($key, $value);
        }

        $stmt->execute();
    }

    /**
     * Executes a prepared query and returns all results as an associative array.
     */
    public function queryAll(string $query, array $params = []): array
    {
        $stmt = $this->dbh->prepare($query);
        if ($stmt->execute($params)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }
}
