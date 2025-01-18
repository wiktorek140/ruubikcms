<?php

namespace Ruubik\Service\Network;

interface RequestInterface
{
    /**
     * Get a parameter from the query string ($_GET).
     *
     * @param string $key The key of the parameter.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getQueryParam(string $key, $default = null);

    /**
     * Get all query parameters ($_GET).
     *
     * @return array
     */
    public function getQueryParams(): array;

    /**
     * Get a parameter from the POST body ($_POST).
     *
     * @param string $key The key of the parameter.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getBodyParam(string $key, $default = null);

    /**
     * Get all body parameters ($_POST).
     *
     * @return array
     */
    public function getBodyParams(): array;

    /**
     * Get an uploaded file from $_FILES.
     *
     * @param string $key The key of the file.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getFile(string $key, $default = null);

    /**
     * Get all uploaded files ($_FILES).
     *
     * @return array
     */
    public function getFiles(): array;

    /**
     * Get a cookie from $_COOKIE.
     *
     * @param string $key The key of the cookie.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getCookie(string $key, $default = null);

    /**
     * Get all cookies ($_COOKIE).
     *
     * @return array
     */
    public function getCookies(): array;

    /**
     * Get a server parameter from $_SERVER.
     *
     * @param string $key The key of the server parameter.
     * @param mixed $default Default value if the key does not exist.
     * @return mixed
     */
    public function getServerParam(string $key, $default = null);

    /**
     * Get all server parameters ($_SERVER).
     *
     * @return array
     */
    public function getServerParams(): array;
}
