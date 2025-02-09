<?php

namespace Ruubik\Service\Html;

final class HTMLHelper
{
    public function __construct(private readonly string $charset = 'UTF-8')
    {
    }

    /**
     * Escapes input for XSS prevention.
     */
    public function escape(string $input): string
    {
        return stripslashes(htmlentities($input, ENT_COMPAT, $this->charset));
    }

    /**
     * Cuts text to a certain length, avoiding cutting words in half.
     */
    public function snippetString(string $text, int $length, string $tail = '...'): string
    {
        if ($length < 1 || $length > 10000) {
            $length = 110;
        }

        $text = trim($text);
        if (strlen($text) > $length) {
            for ($i = 1; $text[$length - $i] !== ' '; $i++) {
                if ($i === $length) {
                    return substr($text, 0, $length) . $tail;
                }
            }

            for (; in_array($text[$length - $i], [',', '.', ' ']); $i++) {
            }

            $text = substr($text, 0, $length - $i + 1) . $tail;
        }

        return $text;
    }
}
