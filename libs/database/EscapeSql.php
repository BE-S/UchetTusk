<?php

namespace Libs\Database;

class EscapeSql
{
    const comparison = [
        ">"              => true,
        "<"              => true,
        "="              => true,
        ">="             => true,
        "<="             => true,
        "like"           => true,
        "ilike"           => true,
        "!="             => true,
        "<>"             => true,
        "is null"        => true,
        "is not null"    => true,
        "is true"        => true,
        "is not true"    => true,
        "is false"       => true,
        "is not false"   => true,
        "is unknown"     => true,
        "is not unknown" => true,
    ];
    
    const sqlCommandForValidation = "^((?i)SELECT|WHERE|FROM|ORDER|GROUP BY|ORDER BY|LIMIT|INSERT|UPDATE|DELETE|DROP|TRUNCATE|UNION)$";

    const symbolsForEscape = [
        "\x00",
        "\n",
        "\r",
        "\\",
        "'",
        "\"",
        "\x1a",
    ];

    public static function escape(string $value): string
    {
        $value = trim($value);

        foreach (self::symbolsForEscape as $symbol) {
            $value = str_replace($symbol, "\\$symbol", $value);
        }

        return $value;
    }

    public static function checkExistComparison(string $comparison): bool
    {
        return isset(self::comparison[$comparison]);
    }

    public static function checkSubqueryInColumn(string $column): bool
    {
        return (bool) preg_match_all(self::sqlCommandForValidation, $column);
    }
}