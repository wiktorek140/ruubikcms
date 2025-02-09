<?php

namespace Ruubik\Controller;

use Ruubik\Service\SystemCheckService;
use Ruubik\Service\Network\RequestInterface;
use Ruubik\Service\Network\ResponseInterface;

class InstallCheckController extends Controller
{
    private SystemCheckService $systemCheckService;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response);
        $this->systemCheckService = new SystemCheckService();
    }

    public function checkSystem(): void
    {
        $requiredExtensions = ['PDO', 'pdo_sqlite'];
        $writableDirs = [
            'ruubikcms/sqlite' => '../sqlite',
            'ruubikcms/useruploads' => '../useruploads',
        ];

        $phpVersionOk = $this->systemCheckService->checkPhpVersion();
        $missingExtensions = $this->systemCheckService->checkExtensions($requiredExtensions);
        $unwritableDirs = $this->systemCheckService->checkWritableDirectories($writableDirs);
        $errors = $this->systemCheckService->getErrors();
        $installationSuccessful = $this->systemCheckService->isInstallationSuccessful();

$this->response->setBody($this->getTwig()->render('check/check.twig',  [
            'phpVersion' => PHP_VERSION,
            'phpVersionOk' => $phpVersionOk,
            'extensions' => $requiredExtensions,
            'missingExtensions' => $missingExtensions,
            'writableDirs' => $writableDirs,
            'unwritableDirs' => $unwritableDirs,
            'errors' => $errors,
            'installationSuccessful' => $installationSuccessful,
        ]));
    }
}