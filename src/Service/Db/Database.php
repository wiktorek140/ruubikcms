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
    public function query(string $query, ?array $bindParams = null, ?string $order = null);

    /**
     * @param string $query
     * @param array|null $bindParams
     * @param string|null $order
     * @return void
     */
    public function execute(string $query, ?array $bindParams = null): void;
}
