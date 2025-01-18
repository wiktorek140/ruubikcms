<?php

namespace Ruubik\Application;

use Exception;
use Ruubik\Service\RouteLoader;
use Ruubik\Service\Network\RequestInterface;
use Ruubik\Service\Network\ResponseInterface;

class App
{
    /**
     * @var array Routes definition.
     */
    protected array $routes = [];

    /**
     * @var RouteLoader Instance of the RouteLoader.
     */
    protected RouteLoader $routeLoader;

    /**
     * App constructor.
     *
     * @param RouteLoader $routeLoader The route loader instance.
     */
    public function __construct(RouteLoader $routeLoader)
    {
        $this->routeLoader = $routeLoader;
        $this->loadRoutes();
    }

    /**
     * Register a route with a specific controller and action.
     *
     * @param string $method HTTP method (e.g., GET, POST).
     * @param string $path Route path (e.g., /users).
     * @param callable $handler A callable function or [ControllerClass, 'method'].
     * @return void
     */
    public function registerRoute(string $method, string $path, callable $handler): void
    {
        $method = strtoupper($method);
        $this->routes[$method][$path] = $handler;
    }

    /**
     * Load routes using the RouteLoader.
     *
     * @return void
     * @throws Exception If routes are invalid or cannot be loaded.
     */
    protected function loadRoutes(): void
    {
        $routes = $this->routeLoader->loadRoutes();

        foreach ($routes as $route) {
            $method = $route['method'] ?? 'GET';
            $path = $route['path'] ?? '/';
            $handler = $route['handler'] ?? null;

            if (!$handler || !is_callable($handler)) {
                throw new Exception("Invalid handler for route: {$path}");
            }

            $this->registerRoute($method, $path, $handler);
        }
    }

    /**
     * Handle the incoming request, call the appropriate controller, and return the response.
     *
     * @param RequestInterface $request The incoming request.
     * @param ResponseInterface $response The response to populate.
     * @return void
     */
    public function handle(RequestInterface $request, ResponseInterface $response): void
    {
        $method = strtoupper($request->getServerParam('REQUEST_METHOD', 'GET'));
        $path = $request->getServerParam('REQUEST_URI', '/');

        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            $response->setStatusCode(404);
            $response->setBody(json_encode(['error' => 'Route not found']));
            $response->setHeader('Content-Type', 'application/json');
            return;
        }

        try {
            $result = call_user_func($handler, $request, $response);

            if (is_string($result)) {
                $response->setBody($result);
            }
        } catch (Exception $e) {
            $response->setStatusCode(500);
            $response->setBody(json_encode(['error' => $e->getMessage()]));
            $response->setHeader('Content-Type', 'application/json');
        }
    }
}
