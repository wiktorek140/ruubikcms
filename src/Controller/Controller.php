<?php

namespace Ruubik\Conttoller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    private $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/templates');
        $this->twig = new Environment($loader);
    }

    public function getTwig()
    {
        return $this->twig;
    }
}
