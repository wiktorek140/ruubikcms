<?php

namespace Ruubik\Service\Network;

class Request implements RequestInterface
{
    /**
     * @var array Query string parameters ($_GET).
     */
    protected array $query;

    /**
     * @var array POST data ($_POST).
     */
    protected array $body;

    /**
     * @var array Uploaded files ($_FILES).
     */
    protected array $files;

    /**
     * @var array Cookies ($_COOKIE).
     */
    protected array $cookies;

    /**
     * @var array Server data ($_SERVER).
     */
    protected array $server;

    /**
     * Request constructor.
     *
     * @param array|null $query   Optional query parameters (default: $_GET).
     * @param array|null $body    Optional body parameters (default: $_POST).
     * @param array|null $files   Optional uploaded files (default: $_FILES).
     * @param array|null $cookies Optional cookies (default: $_COOKIE).
     * @param array|null $server  Optional server data (default: $_SERVER).
     */
    public function __construct(
        ?array $query = null,
        ?array $body = null,
        ?array $files = null,
        ?array $cookies = null,
        ?array $server = null
    ) {
        $this->query = $query ?? $_GET;
        $this->body = $body ?? $_POST;
        $this->files = $files ?? $_FILES;
        $this->cookies = $cookies ?? $_COOKIE;
        $this->server = $server ?? $_SERVER;
    }

    /**
     * Get a parameter from the query string ($_GET).
     *
     * @param string $key The key of the parameter.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getQueryParam(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Get a parameter from the POST body ($_POST).
     *
     * @param string $key The key of the parameter.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getBodyParam(string $key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * Get an uploaded file from $_FILES.
     *
     * @param string $key The key of the file.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getFile(string $key, $default = null)
    {
        return $this->files[$key] ?? $default;
    }

    /**
     * Get a cookie from $_COOKIE.
     *
     * @param string $key The key of the cookie.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getCookie(string $key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Get a server parameter from $_SERVER.
     *
     * @param string $key The key of the server parameter.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getServerParam(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Get all query parameters ($_GET).
     *
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->query;
    }

    /**
     * Get all body parameters ($_POST).
     *
     * @return array
     */
    public function getBodyParams(): array
    {
        return $this->body;
    }

    /**
     * Get all uploaded files ($_FILES).
     *
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Get all cookies ($_COOKIE).
     *
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * Get all server parameters ($_SERVER).
     *
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->server;
    }
}
