<?php

declare(strict_types=1);

namespace Domain;

class StringCalculator
{
    public function add(string $numbers): int
    {
        if ($numbers === '') {
            return 0;
        }

        $numbers = explode(',', $numbers);

        if (count($numbers) === 1) {
            return (int) $numbers[0];
        }

        return 0;
    }
}
