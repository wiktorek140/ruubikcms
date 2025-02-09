<?php

namespace Ruubik\Tests\Service\DI;

use PHPUnit\Framework\TestCase;
use Ruubik\Service\DI\Container;
use Ruubik\Service\Db\Database;

final class ContainerTest extends TestCase
{
    public function testCanResolveSimpleClass()
    {
        $container = new Container();
        $container->bind(Database::class, TestDatabase::class);
        $db = $container->make(Database::class);
        $this->assertInstanceOf(TestDatabase::class, $db);
    }

    public function testCanResolveClassWithDependencies()
    {
        $container = new Container();
        $container->bind(Database::class, TestDatabase::class);
        $app = $container->make(TestApp::class);
        $this->assertInstanceOf(TestApp::class, $app);
    }

    public function testCanRegisterAndResolveSingleton()
    {
        $container = new Container();
        $container->singleton(Database::class, TestDatabase::class);

        $logger1 = $container->make(Database::class);
        $logger2 = $container->make(Database::class);

        $this->assertSame($logger1, $logger2);
    }
}
