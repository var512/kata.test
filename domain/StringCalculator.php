<?php

declare(strict_types=1);

namespace Domain;

class StringCalculator
{
    private array $separators;

    public function __construct()
    {
        $this->separators = [',', '\n'];
    }

    public function add(string $numbers): int
    {
        if ($numbers === '') {
            return 0;
        }

        $numbers = str_replace($this->separators, ',', $numbers);
        $numbers = explode(',', $numbers);

        if (count($numbers) === 1) {
            return (int) $numbers[0];
        }

        return array_sum($numbers);
    }
}
