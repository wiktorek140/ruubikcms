<?php

namespace Ruubik\Service;

final class SystemCheckService
{
    private array $errors = [];

    public function checkPhpVersion(): bool
    {
        if (version_compare(PHP_VERSION, '5.1.0') < 0) {
            $this->errors[] = 'PHP version must be at least 5.1.0. Your version: ' . PHP_VERSION;
            return false;
        }
        return true;
    }

    public function checkExtensions(array $extensions): array
    {
        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                $this->errors[] = "PHP extension $extension must be enabled";
            }
        }
        return $this->errors;
    }

    public function checkWritableDirectories(array $directories): array
    {
        foreach ($directories as $key => $path) {
            if (!is_writable($path)) {
                $this->errors[] = "$key must be writable";
            }
        }
        return $this->errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isInstallationSuccessful(): bool
    {
        return empty($this->errors);
    }
}
