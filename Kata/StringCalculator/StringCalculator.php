<?php

declare(strict_types=1);

namespace Kata\StringCalculator;

use App\Events\AddOccurred;
use App\Exceptions\NegativeNumbersAreNotAllowed;

class StringCalculator
{
    private array $delimiters;
    private int $calledCount = 0;

    public function __construct()
    {
        $this->delimiters = [',', '\n'];
    }

    /**
     * Returns how many times add() was invoked.
     *
     * @return int
     */
    public function getCalledCount(): int
    {
        return $this->calledCount;
    }

    /**
     * @throws NegativeNumbersAreNotAllowed
     */
    public function add(string $numbers): int
    {
        AddOccurred::dispatch($this->calledCount++);

        $customDelimiter = null;

        if ($numbers === '') {
            return 0;
        }

        preg_match('/^\/\/(.)\\\n(.*)|^\/\/\[(.*)]\\\n(.*)/', $numbers, $customDelimiter);
        $customDelimiterIndex = count($customDelimiter) === 5 ? 3 : 1;

        if (isset($customDelimiter[$customDelimiterIndex])) {
            array_push($this->delimiters, $customDelimiter[$customDelimiterIndex]);
            $numbers = preg_replace('/^(\/\/.*?\\\n)/', '', $numbers);
        }

        $numbers = str_replace($this->delimiters, ',', $numbers);
        $numbers = explode(',', $numbers);

        $negativeNumbers = array_filter($numbers, fn ($n) => $n < 0);
        if (count($negativeNumbers) > 0) {
            throw new NegativeNumbersAreNotAllowed('negatives not allowed ' . implode(' ', $negativeNumbers));
        }

        $numbers = array_filter($numbers, fn ($n) => $n <= 1000);

        return array_sum($numbers);
    }
}
