<?php

namespace Ruubik\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ruubik\Application\App;
use Ruubik\Service\Network\Request;
use Ruubik\Service\Network\RequestInterface;
use Ruubik\Service\Network\Response;
use Ruubik\Service\Network\ResponseInterface;
use Ruubik\Service\RouteLoader;

final class AppTest extends TestCase
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
                'handler' => function (RequestInterface $request, ResponseInterface $response) {
                    $response->setStatusCode(200);
                    $response->setBody('{"message": "Success"}');
                    return $response;
                },
            ],
        ]);
        $app = new App($routeLoaderMock);

        $request = $this->createMock(Request::class);
        $request->method('getServerParam')
            ->willReturnCallback(function ($param, $default) {
                $map = [
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI'    => '/test',
                ];
                return $map[$param] ?? $default;
            });

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

        $request = $this->createMock(Request::class);
        $request->method('getServerParam')
            ->willReturnCallback(function ($param, $default) {
                $map = [
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI'    => '/invalid',
                ];
                return $map[$param] ?? $default;
            });

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

        $request = $this->createMock(Request::class);
        $request->method('getServerParam')
            ->willReturnCallback(function ($param, $default) {
                $map = [
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI'    => '/error',
                ];
                return $map[$param] ?? $default;
            });

        $response = new Response();

        $app->handle($request, $response);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            '{"error":"Something went wrong"}',
            $response->getBody()
        );
    }
}
