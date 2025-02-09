<?php

namespace Ruubik\Service;

use Symfony\Component\Yaml\Yaml;
use Exception;

final class RouteLoader
{
    /**
     * RouteLoader constructor.
     *
     * @param string $filePath Path to the YAML file containing the routes.
     */
    public function __construct(protected string $filePath)
    {
    }

    /**
     * Load routes from the YAML file.
     *
     * @return array An array of routes.
     * @throws Exception If the file is not readable or invalid.
     */
    public function loadRoutes(): array
    {
        if (!file_exists($this->filePath) || !is_readable($this->filePath)) {
            throw new Exception("Route file not found or unreadable: {$this->filePath}");
        }

        $routes = Yaml::parseFile($this->filePath);

        if (!is_array($routes)) {
            throw new Exception("Invalid route format in YAML file.");
        }

        return $routes;
    }
}
