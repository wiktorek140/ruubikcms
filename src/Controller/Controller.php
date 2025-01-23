<?php

namespace Ruubik\Controller;

use Ruubik\Service\Network\RequestInterface;
use Ruubik\Service\Network\ResponseInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    private readonly Environment $twig;

    public function __construct(
        public RequestInterface $request,
        public ResponseInterface $response
    ) {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $this->twig = new Environment($loader);
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
}
