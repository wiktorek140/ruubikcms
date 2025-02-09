<?php

use PHPUnit\Framework\TestCase;
use Ruubik\Service\SystemCheckService;

class SystemCheckServiceTest extends TestCase
{
    private SystemCheckService $service;

    protected function setUp(): void
    {
        $this->service = new SystemCheckService();
    }

    public function testCheckPhpVersion()
    {
        $result = $this->service->checkPhpVersion();
        $expected = version_compare(PHP_VERSION, '5.1.0', '>=');
        
        $this->assertSame($expected, $result);

        if (!$expected) {
            $this->assertNotEmpty($this->service->getErrors());
        } else {
            $this->assertEmpty($this->service->getErrors());
        }
    }

    public function testCheckExtensionsWithExistingExtensions()
    {
        $extensions = ['json', 'spl']; // Commonly enabled extensions
        $errors = $this->service->checkExtensions($extensions);

        $this->assertEmpty($errors);
        $this->assertEmpty($this->service->getErrors());
    }

    public function testCheckExtensionsWithMissingExtensions()
    {
        $extensions = ['fake_extension_1', 'fake_extension_2'];
        $errors = $this->service->checkExtensions($extensions);

        $this->assertCount(2, $errors);
        $this->assertStringContainsString('PHP extension fake_extension_1 must be enabled', $errors[0]);
        $this->assertStringContainsString('PHP extension fake_extension_2 must be enabled', $errors[1]);
    }

    public function testCheckWritableDirectoriesWithWritableDirectory()
    {
        $tempDir = sys_get_temp_dir(); // System's temp directory should be writable
        $errors = $this->service->checkWritableDirectories(['temp_dir' => $tempDir]);

        $this->assertEmpty($errors);
        $this->assertEmpty($this->service->getErrors());
    }

    public function testCheckWritableDirectoriesWithNonWritableDirectory()
    {
        $errors = $this->service->checkWritableDirectories(['non_writable' => '/root']); // Usually not writable

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('non_writable must be writable', $errors[0]);
    }

    public function testIsInstallationSuccessful()
    {
        $this->service->checkPhpVersion();
        $this->service->checkExtensions(['json']);
        $this->service->checkWritableDirectories(['temp' => sys_get_temp_dir()]);

        if ($this->service->getErrors()) {
            $this->assertFalse($this->service->isInstallationSuccessful());
        } else {
            $this->assertTrue($this->service->isInstallationSuccessful());
        }
    }
}