<?php

namespace App\Domains\Shared\Utils;

class FullTextHelper
{
    /**
     * Monta uma expressão de busca em modo boolean (MATCH ... AGAINST ... IN BOOLEAN MODE)
     * a partir de um termo digitado pelo usuário, adicionando prefixo wildcard e exigindo
     * a presença de cada palavra (AND).
     */
    public static function toBooleanQuery(string $term): string
    {
        $words = preg_split('/\s+/', trim($term), -1, PREG_SPLIT_NO_EMPTY);

        if (empty($words)) {
            return '';
        }

        return collect($words)
            ->map(fn (string $word) => '+' . self::escape($word) . '*')
            ->implode(' ');
    }

    /**
     * Remove caracteres reservados do modo boolean do MySQL/MariaDB
     * (+ - > < ( ) ~ * " @).
     */
    private static function escape(string $word): string
    {
        return preg_replace('/[+\-><()~*"@]/', '', $word);
    }
}
