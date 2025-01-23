<?php

namespace Ruubik\Controller;

use Ruubik\Service\Network\RequestInterface;
use Ruubik\Service\Network\ResponseInterface;
use Ruubik\View\Index\IndexService;

class IndexController extends Controller
{
    private IndexService $indexService;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response);
        // Inicjalizacja serwisu
        $this->indexService = new IndexService();
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
        $this->response->setBody($this->getTwig()->render('index/index.twig', $pageData));
    }
}
