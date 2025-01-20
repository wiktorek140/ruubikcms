<?php

namespace Ruubik\Tests;

use PHPUnit\Framework\TestCase;
use Ruubik\Service\Network\Response;

class ResponseTest extends TestCase
{
    public function testSetAndGetStatusCode(): void
    {
        $response = new Response();
        $response->setStatusCode(200);
        $this->assertEquals(200, $response->getStatusCode());

        $response->setStatusCode(404);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testSetAndGetHeaders(): void
    {
        $response = new Response();

        $response->setHeader('Content-Type', 'application/json');
        $this->assertEquals(['Content-Type' => 'application/json'], $response->getHeaders());

        $response->setHeader('Cache-Control', 'no-cache');
        $this->assertEquals(
            [
                'Content-Type'  => 'application/json',
                'Cache-Control' => 'no-cache',
            ],
            $response->getHeaders()
        );
    }

    public function testSetAndGetBody(): void
    {
        $response = new Response();

        $response->setBody('{"message": "Hello, world!"}');
        $this->assertEquals('{"message": "Hello, world!"}', $response->getBody());

        $response->setBody('{"error": "Not found"}');
        $this->assertEquals('{"error": "Not found"}', $response->getBody());
    }

    public function testSend(): void
    {
        $response = new Response();
        $response->setStatusCode(200);
        $response->setHeader('Content-Type', 'application/json');
        $response->setBody('{"message": "Test"}');

        ob_start();
        $response->send();
        $output = ob_get_clean();

        $this->assertEquals('{"message": "Test"}', $output);
    }
}
