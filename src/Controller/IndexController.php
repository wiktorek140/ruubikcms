<?php

namespace Ruubik\Conttoller;

use Ruubik\View\Index\IndexService;

class IndexController extends Controller
{
    private $indexService;

    public function __construct()
    {
        parent::__construct();
        // Inicjalizacja serwisu
        $this->indexeService = new IndexService();
    }

    public function renderPage(): void
    {
        // Pobranie danych z serwisu
        $pageData = $this->indexService->getPageData();

        // Dodanie dodatkowych danych
        $pageData['siteroot'] = $this->indexService->getSiteRoot();
        $pageData['isMobile'] = $this->indexService->isMobile();
        $pageData['snippet_php'] = [
            $this->indexService,
            'snippetPHP',
        ];
        $pageData['snippet'] = [
            $this->indexService,
            'snippet',
        ];

        // Renderowanie widoku
        echo $this->getTwig()->render('index.html.twig', $pageData);
    }
}
