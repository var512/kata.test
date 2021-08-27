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
        $this->calledCount++;
        AddOccurred::dispatch();

        $customDelimiter = null;

        if ($numbers === '') {
            return 0;
        }

        preg_match('/^\/\/(.)\\\n(.*)/', $numbers, $customDelimiter);

        if (isset($customDelimiter[1])) {
            array_push($this->delimiters, $customDelimiter[1]);
            $numbers = mb_substr($numbers, 5);
        }

        $numbers = str_replace($this->delimiters, ',', $numbers);
        $numbers = explode(',', $numbers);

        $negativeNumbers = array_filter($numbers, fn ($n) => $n < 0);
        if (count($negativeNumbers) > 0) {
            throw new NegativeNumbersAreNotAllowed('negatives not allowed '.implode(' ', $negativeNumbers));
        }

        return array_sum($numbers);
    }
}
