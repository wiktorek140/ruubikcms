<?php

namespace Ruubik\Service\Network;

interface ResponseInterface
{
    /**
     * Set the HTTP status code.
     *
     * @param int $code The HTTP status code.
     * @return void
     */
    public function setStatusCode(int $code): void;

    /**
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Set a header value.
     *
     * @param string $name The name of the header.
     * @param string $value The value of the header.
     * @return void
     */
    public function setHeader(string $name, string $value): void;

    /**
     * Get all headers.
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Set the response body.
     *
     * @param string $body The response body content.
     * @return void
     */
    public function setBody(string $body): void;

    /**
     * Get the response body.
     *
     * @return string
     */
    public function getBody(): string;

    /**
     * Send the response to the client.
     *
     * @return void
     */
    public function send(): void;
}
