<?php

namespace Ruubik\Service\Network;

class Response implements ResponseInterface
{
    /**
     * @var integer HTTP status code.
     */
    protected int $statusCode = 200;

    /**
     * @var array Response headers.
     */
    protected array $headers = [];

    /**
     * @var string Response body.
     */
    protected string $body = '';

    /**
     * Set the HTTP status code.
     *
     * @param int $code The HTTP status code.
     * @return void
     */
    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set a header value.
     *
     * @param string $name The name of the header.
     * @param string $value The value of the header.
     * @return void
     */
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * Get all headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set the response body.
     *
     * @param string $body The response body content.
     * @return void
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * Get the response body.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Send the response to the client.
     *
     * @return void
     */
    public function send(): void
    {
        // Set the HTTP status code
        http_response_code($this->statusCode);

        // Set headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Output the body
        echo $this->body;
    }
}
