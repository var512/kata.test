<?php

declare(strict_types=1);

namespace Kata\StringCalculator;

use App\Events\AddOccurred;
use App\Exceptions\InvalidMetadataException;
use App\Exceptions\NegativeNumbersAreNotAllowed;

class StringCalculator
{
    private array $delimiters;
    private int $calledCount = 0;
    private string $specificDelimiterPattern;
    private string $anyLengthDelimiterPattern;

    public function __construct()
    {
        $this->delimiters = [',', '\n'];
        $this->specificDelimiterPattern = '^\/\/(.)\\\n(.*)';
        $this->anyLengthDelimiterPattern = '^\/\/\[(.*)]\\\n(.*)';
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
     * @throws InvalidMetadataException
     */
    public function add(string $numbers): int
    {
        AddOccurred::dispatch($this->calledCount++);

        if ($numbers === '') {
            return 0;
        }

        $customDelimiter = $this->getCustomDelimiter($numbers);

        if ($customDelimiter !== null) {
            array_push($this->delimiters, $customDelimiter);
        }

        $numbers = $this->removeMetadata($numbers);
        $numbers = str_replace($this->delimiters, ',', $numbers);
        $numbers = explode(',', $numbers);

        $negativeNumbers = array_filter($numbers, fn ($n) => $n < 0);
        if (count($negativeNumbers) > 0) {
            throw new NegativeNumbersAreNotAllowed('negatives not allowed ' . implode(' ', $negativeNumbers));
        }

        $numbers = array_filter($numbers, fn ($n) => $n <= 1000);

        return array_sum($numbers);
    }

    /**
     * Returns the custom delimiter.
     *
     * @param string $numbers
     *
     * @return string|null
     */
    protected function getCustomDelimiter(string $numbers): ?string
    {
        $customDelimiter = null;

        preg_match(
            '/' . $this->specificDelimiterPattern . '|' . $this->anyLengthDelimiterPattern . '/',
            $numbers,
            $customDelimiter,
        );

        $customDelimiterIndex = count($customDelimiter) === 5 ? 3 : 1;

        if (isset($customDelimiter[$customDelimiterIndex])) {
            return $customDelimiter[$customDelimiterIndex];
        }

        return null;
    }

    /**
     * Removes metadata from the input string.
     *
     * @param string $numbers
     *
     * @throws InvalidMetadataException
     *
     * @return string
     */
    protected function removeMetadata(string $numbers): string
    {
        $numbers = preg_replace('/^(\/\/.*?\\\n)/', '', $numbers);

        if ($numbers === null || is_array($numbers)) {
            throw new InvalidMetadataException();
        }

        return $numbers;
    }
}
