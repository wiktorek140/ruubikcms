<?php

namespace Ruubik\Tests\Service\DI;

use Ruubik\Service\Db\Database;

class TestApp
{
    private Database $logger;

    public function __construct(Database $logger)
    {
        $this->logger = $logger;
    }

    public function run()
    {
        $this->logger->query("App is running!");
    }
}