<?php

namespace Ruubik\Service\Utils;

use Ruubik\Service\Db\Database;

final readonly class PageHelper
{
    public function __construct(private Database $dbHelper)
    {
    }

    public function getSiteData(): array
    {
        return $this->dbHelper->query('SELECT * FROM site WHERE id = 1')[0] ?? [];
    }

    public function getPageData(string $pageUrl, bool $onlyPublished = false, string $table = 'page'): ?array
    {
        $statusQuery = $onlyPublished ? ' AND STATUS = 1' : '';
        $sql = 'SELECT * FROM ' . $table . ' WHERE pageurl = ?' . $statusQuery;

        return $this->dbHelper->query($sql, [$pageUrl])[0] ?? null;
    }

    public function getExtraPageData(string $pageUrl, bool $onlyPublished = false): ?array
    {
        return $this->getPageData($pageUrl, $onlyPublished, 'extrapage');
    }

    public function getCmsOptions(): ?array
    {
        return $this->dbHelper->query('SELECT * FROM options WHERE id = 1')[0] ?? null;
    }

    public function getFrontpageValue(string $column = 'pageurl', string $table = 'page'): mixed
    {
        $sql = 'SELECT ' . $column . ' FROM ' . $table . ' WHERE levelnum = 1 AND status = 1 ORDER BY ordernum LIMIT 1';
        return $this->dbHelper->query($sql)[0][$column] ?? null;
    }

    public function cleanUrl(string $pageUrl, string $table = 'page'): ?string
    {
        $level = $this->dbHelper->query('SELECT levelnum FROM ' . $table . ' WHERE pageurl = ?', [$pageUrl]);
        $siteData = $this->getSiteData();

        $siteRoot = rtrim($siteData['siteroot'] ?? '', '/');
        $urlSuffix = trim($siteData['url_suffix'] ?? '', '.');

        if ($urlSuffix) {
            $urlSuffix = '.' . $urlSuffix;
        }

        $urlBase = '/' . ($siteRoot ? $siteRoot . '/' : '') . ($table === 'extrapage' ? 'extra/' : '') . 'index.php/';
        if ($level > 1) {
            $mother = $this->dbHelper->query('SELECT mother FROM ' . $table . ' WHERE pageurl = ?', [$pageUrl]);
        }

        if ($level <= 1) {
            $urlRest = $pageUrl . $urlSuffix;
        } else if ($level === 2) {
            $urlRest = $mother . '/' . $pageUrl . $urlSuffix;
        } else if ($level === 3) {
            $grandmother = $this->dbHelper->query('SELECT mother FROM ' . $table . ' WHERE pageurl = ?', [$mother]);
            $urlRest = $grandmother . '/' . $mother . '/' . $pageUrl . $urlSuffix;
        } else {
            return null;
        }

        return $urlBase . $urlRest;
    }
}
