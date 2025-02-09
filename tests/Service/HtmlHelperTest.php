<?php

namespace Ruubik\Tests\Service;

use PHPUnit\Framework\TestCase;
use Ruubik\Service\Html\HTMLHelper;

final class HTMLHelperTest extends TestCase
{
    public function testEscape()
    {
        $helper = new HTMLHelper('UTF-8');
        $input = "<script>alert('XSS');</script>";
        $expected = "&lt;script&gt;alert('XSS');&lt;/script&gt;";
        $this->assertEquals($expected, $helper->escape($input));
    }

    public function testSnippetStringShorterThanLimit()
    {
        $helper = new HTMLHelper();
        $input = "This is a short string.";
        $this->assertEquals($input, $helper->snippetString($input, 50));
    }

    public function testSnippetStringLongerThanLimit()
    {
        $helper = new HTMLHelper();
        $input = "This is a longer string that should be truncated.";
        $expected = "This is a longer string that...";
        $this->assertEquals($expected, $helper->snippetString($input, 30));
    }
}
