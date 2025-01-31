<?php

namespace Ruubik\Service\DI;

use \Exception;
use \ReflectionClass;
use Symfony\Component\Yaml\Yaml;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    /**
     * Bind an interface or class to a concrete implementation.
     */
    public function bind(string $abstract, callable|string $concrete, bool $singleton = false)
    {
        $this->bindings[$abstract] = compact('concrete', 'singleton');
    }

    /**
     * Bind a singleton instance.
     */
    public function singleton(string $abstract, callable|string $concrete)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Resolve a class or interface.
     */
    public function make(string $abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            return $this->resolve($abstract);
        }

        $binding = $this->bindings[$abstract];

        $object = is_callable($binding['concrete'])
            ? $binding['concrete']($this)
            : $this->resolve($binding['concrete']);

        if ($binding['singleton']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Automatically resolve class dependencies.
     */
    private function resolve(string $class)
    {
        if (!class_exists($class)) {
            throw new Exception("Class {$class} not found.");
        }

        $reflection = new ReflectionClass($class);
        if (!$constructor = $reflection->getConstructor()) {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = array_map(fn($param) => $this->make($param->getType()->getName()), $parameters);

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Load bindings from a YAML configuration file.
     */
    public function loadFromYaml(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception("YAML file not found: $filePath");
        }

        $config = Yaml::parseFile($filePath);
        
        foreach ($config['services'] as $abstract => $definition) {
            $singleton = $definition['singleton'] ?? false;
            $this->bind($abstract, $definition['class'], $singleton);
        }
    }
}