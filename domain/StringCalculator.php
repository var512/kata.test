<?php

declare(strict_types=1);

namespace Domain;

class StringCalculator
{
    private array $delimiters;

    public function __construct()
    {
        $this->delimiters = [',', '\n'];
    }

    public function add(string $numbers): int
    {
        $customDelimiter = null;

        if ($numbers === '') {
            return 0;
        }

        preg_match('/^\/\/(.)\\\n(.*)/', $numbers, $customDelimiter);

        if (isset($customDelimiter[1])) {
            array_push($this->delimiters, $customDelimiter[1]);
        }

        $numbers = str_replace($this->delimiters, ',', $numbers);
        $numbers = explode(',', $numbers);

        if (count($numbers) === 1) {
            return (int) $numbers[0];
        }

        return array_sum($numbers);
    }
}
