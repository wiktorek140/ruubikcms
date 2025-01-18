<?php

class IndexService
{
    /**
     * Pobiera dane potrzebne do widoku.
     *
     * @return array
     */
    public function getPageData(): array
    {
        require 'ruubikcms/page.php';

        return [
            'doctype'      => $page['doctype'] ?? '<!DOCTYPE html>',
            'lang'         => $page['lang'] ?? 'pl',
            'title'        => $page['title'] ?? 'Moja Strona',
            'gacode'       => $page['gacode'] ?? '',
            'charset'      => $page['charset'] ?? 'UTF-8',
            'description'  => $page['description'] ?? 'Opis strony',
            'keywords'     => $page['keywords'] ?? 'keywords',
            'robots'       => $page['robots'] ?? 'index, follow',
            'author'       => $page['author'] ?? 'Nieznany Autor',
            'copyright'    => $page['copyright'] ?? 'Nieznany Copyright',
            'sitename'     => $page['sitename'] ?? 'Moja Strona',
            'mainmenu'     => $page['mainmenu'] ?? '',
            'dropdownmenu' => $page['dropdownmenu'] ?? '',
            'submenu1'     => $page['submenu1'] ?? '',
            'header1'      => $page['header1'] ?? 'Nagłówek strony',
            'content'      => $page['content'] ?? '<p>Treść domyślna strony.</p>',
        ];
    }

    /**
     * Zwraca dynamiczną ścieżkę do katalogu witryny.
     *
     * @return string
     */
    public function getSiteRoot(): string
    {
        return '/ruubikcms/website/';
    }

    /**
     * Sprawdza, czy użytkownik korzysta z urządzenia mobilnego.
     *
     * @return bool
     */
    public function isMobile(): bool
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/mobile|android|iphone/i', $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * Załącza snippet PHP.
     *
     * @param string $name Nazwa snippetu.
     * @return string
     */
    public function snippetPHP(string $name): string
    {
        ob_start();
        snippet_php($name); // Zakładamy, że funkcja `snippet` jest dostępna
        return ob_get_clean();
    }

    /**
     * Załącza snippet Twig.
     *
     * @param string $name Nazwa snippetu.
     * @return string
     */
    public function snippet(string $name): string
    {
        return snippet($name);
    }
}
