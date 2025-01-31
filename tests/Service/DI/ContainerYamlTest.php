<?php

namespace Ruubik\Tests\Service\DI;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Ruubik\Service\DI\Container;
use Ruubik\Service\Db\Database;

class ContainerYamlTest extends TestCase
{
    public function testLoadDependenciesFromYaml()
    {
        $yamlContent = <<<YAML
services:
  Ruubik\Service\Db\Database:
    class: Ruubik\Tests\Service\DI\TestDatabase
    singleton: true

  TestApp:
    class: TestApp
YAML;

        file_put_contents('test_services.yaml', $yamlContent);

        $container = new Container();
        $container->loadFromYaml('test_services.yaml');

        $logger = $container->make(Database::class);
        $app = $container->make(TestApp::class);

        $this->assertInstanceOf(TestDatabase::class, $logger);
        $this->assertInstanceOf(TestApp::class, $app);

        unlink('test_services.yaml'); // Clean up
    }
}
