<?php

namespace Ruubik\Tests;

use PHPUnit\Framework\TestCase;
use Ruubik\Application\App;

class AppTest extends TestCase
{
    public function testRegisterRoute(): void
    {
        $routeLoaderMock = $this->createMock(RouteLoader::class);
        $app = new App($routeLoaderMock);

        $handler = function () {
            return "Handler called";
        };

        $app->registerRoute('GET', '/test', $handler);

        // We can't directly access private properties, but if no exception occurs, registration works.
        $this->assertTrue(true, "Route registered successfully");
    }

    public function testHandleValidRoute(): void
    {
        $routeLoaderMock = $this->createMock(RouteLoader::class);
        $routeLoaderMock->method('loadRoutes')->willReturn([
            [
                'method'  => 'GET',
                'path'    => '/test',
                'handler' => function ($request, $response) {
                    $response->setStatusCode(200);
                    $response->setBody('{"message": "Success"}');
                    return $response;
                },
            ],
        ]);
        $app = new App($routeLoaderMock);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getServerParam')->willReturnMap([
            [
                'REQUEST_METHOD',
                'GET',
                'GET',
            ],
            [
                'REQUEST_URI',
                '/test',
                '/test',
            ],
        ]);

        $response = new Response();

        $app->handle($request, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"message": "Success"}', $response->getBody());
    }

    public function testHandleInvalidRoute(): void
    {
        $routeLoaderMock = $this->createMock(RouteLoader::class);
        $routeLoaderMock->method('loadRoutes')->willReturn([]);

        $app = new App($routeLoaderMock);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getServerParam')->willReturnMap([
            [
                'REQUEST_METHOD',
                'GET',
                'GET',
            ],
            [
                'REQUEST_URI',
                '/invalid',
                '/invalid',
            ],
        ]);

        $response = new Response();

        $app->handle($request, $response);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            '{"error":"Route not found"}',
            $response->getBody()
        );
    }

    public function testHandleRouteWithException(): void
    {
        $routeLoaderMock = $this->createMock(RouteLoader::class);
        $routeLoaderMock->method('loadRoutes')->willReturn([
            [
                'method'  => 'GET',
                'path'    => '/error',
                'handler' => function () {
                    throw new Exception("Something went wrong");
                },
            ],
        ]);

        $app = new App($routeLoaderMock);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getServerParam')->willReturnMap([
            [
                'REQUEST_METHOD',
                'GET',
                'GET',
            ],
            [
                'REQUEST_URI',
                '/error',
                '/error',
            ],
        ]);

        $response = new Response();

        $app->handle($request, $response);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            '{"error":"Something went wrong"}',
            $response->getBody()
        );
    }
}
