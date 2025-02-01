<?php

namespace Ruubik\Service\Db;

interface Database
{
    /**
     * @param string $query
     * @param array|null $bindParams
     * @param string|null $order
     * @return mixed
     */
    public function query(string $query, ?array $bindParams = null, ?string $order = null): mixed;

    /**
     * @param string $query
     * @param array|null $bindParams
     * @return void
     */
    public function execute(string $query, ?array $bindParams = null): void;

    public function queryAll(string $query, array $params = []): array;
}
