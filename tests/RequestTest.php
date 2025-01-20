<?php

namespace Ruubik\Tests;

use PHPUnit\Framework\TestCase;
use Ruubik\Service\Network\Request;

class RequestTest extends TestCase
{
    public function testGetQueryParam(): void
    {
        $_GET['key'] = 'value';
        $request = new Request();

        $this->assertEquals('value', $request->getQueryParam('key'));
        $this->assertNull($request->getQueryParam('non_existent_key'));
        $this->assertEquals('default', $request->getQueryParam('non_existent_key', 'default'));
    }

    public function testGetBodyParam(): void
    {
        $_POST['key'] = 'value';
        $request = new Request();

        $this->assertEquals('value', $request->getBodyParam('key'));
        $this->assertNull($request->getBodyParam('non_existent_key'));
        $this->assertEquals('default', $request->getBodyParam('non_existent_key', 'default'));
    }

    public function testGetServerParam(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request = new Request();

        $this->assertEquals('POST', $request->getServerParam('REQUEST_METHOD'));
        $this->assertNull($request->getServerParam('non_existent_key'));
        $this->assertEquals('default', $request->getServerParam('non_existent_key', 'default'));
    }
}
