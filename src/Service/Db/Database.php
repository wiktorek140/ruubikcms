<?php

interface Database
{

    public function query(string $query, ?array $bindParams = null, ?string $order = null);
}