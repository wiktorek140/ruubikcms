<?php

namespace Ruubik\Tests\Controller;

use Exception;
use PHPUnit\Framework\TestCase;
use Ruubik\Application\App;
use Ruubik\Service\Network\Request;
use Ruubik\Service\Network\RequestInterface;
use Ruubik\Service\Network\Response;
use Ruubik\Service\Network\ResponseInterface;
use Ruubik\Service\RouteLoader;

class IndexControllerTest extends TestCase
{
    public function testRegisterRoute(): void
    {

        $routeLoader = new RouteLoader(__DIR__ . '/../../config/route.yml');
        $app = new App($routeLoader);

        $request = $this->createMock(Request::class);
        $request->method('getServerParam')
            ->willReturnCallback(function ($param, $default) {
                $map = [
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI'    => '/user',
                ];
                return $map[$param] ?? $default;
            });

        $response = new Response();

        $app->handle($request, $response);


        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(str_contains($response->getBody(), '<!DOCTYPE html>'));
    }
}
