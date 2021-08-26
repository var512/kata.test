<?php

declare(strict_types=1);

namespace Domain;

use App\Exceptions\NegativeNumbersAreNotAllowed;

class StringCalculator
{
    private array $delimiters;

    public function __construct()
    {
        $this->delimiters = [',', '\n'];
    }

    /**
     * @throws NegativeNumbersAreNotAllowed
     */
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

        $negativeNumbers = array_filter($numbers, fn ($n) => $n < 0);
        if (count($negativeNumbers) > 0) {
            throw new NegativeNumbersAreNotAllowed('negatives not allowed '.$negativeNumbers[0]);
        }

        return array_sum($numbers);
    }
}
