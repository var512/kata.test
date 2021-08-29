<?php

declare(strict_types=1);

namespace Kata\StringCalculator;

use App\Events\AddOccurred;
use App\Exceptions\InvalidMetadataException;
use App\Exceptions\NegativeNumbersAreNotAllowed;

class StringCalculator
{
    /** @var string[] */
    private array $delimiters;

    private int $calledCount = 0;

    private string $specificDelimiterPattern;

    private string $anyLengthDelimiterPattern;

    public function __construct()
    {
        $this->delimiters = [',', '\n'];
        $this->specificDelimiterPattern = '^\/\/(.)\\\n(.*)';
        $this->anyLengthDelimiterPattern = '^\/\/\[(.*)]\\\n(.*)';
        $this->multipleDelimitersPattern = '(\[(.*?)\])';
    }

    /**
     * @throws NegativeNumbersAreNotAllowed
     * @throws InvalidMetadataException
     */
    public function add(string $rawNumbers): int
    {
        AddOccurred::dispatch($this->calledCount++);

        if ($rawNumbers === '') {
            return 0;
        }

        $customDelimiter = $this->getCustomDelimiter($rawNumbers);

        if (count($customDelimiter)) {
            $this->delimiters = array_merge($this->delimiters, $customDelimiter);
        }

        $numbers = $this->unserializeNumbers(
            $this->removeMetadata($rawNumbers)
        );

        $this->guardAgainstNegativeNumbers($numbers);

        $numbers = $this->removeYugeNumbers($numbers);

        return array_sum($numbers);
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
     * Returns the custom delimiter.
     *
     * @param string $rawNumbers
     *
     * @return string[]|null
     */
    protected function getCustomDelimiter(string $rawNumbers): ?array
    {
        $customDelimiter = [];

        preg_match_all('/' . $this->multipleDelimitersPattern . '/', $rawNumbers, $customDelimiter);

        if (count($customDelimiter[0]) > 1) {
            return $customDelimiter[2];
        }

        preg_match(
            '/' . $this->specificDelimiterPattern . '|' . $this->anyLengthDelimiterPattern . '/',
            $rawNumbers,
            $customDelimiter,
        );

        $customDelimiterIndex = count($customDelimiter) === 5 ? 3 : 1;

        if (isset($customDelimiter[$customDelimiterIndex])) {
            return [$customDelimiter[$customDelimiterIndex]];
        }

        return [];
    }

    /**
     * Removes metadata from the input string.
     *
     * @param string $rawNumbers
     *
     * @throws InvalidMetadataException
     *
     * @return string
     */
    protected function removeMetadata(string $rawNumbers): string
    {
        $rawNumbers = preg_replace('/^(\/\/.*?\\\n)/', '', $rawNumbers);

        if ($rawNumbers === null) {
            throw new InvalidMetadataException();
        }

        return $rawNumbers;
    }

    /**
     * Returns the number string as array.
     *
     * @param string $rawNumbers
     *
     * @return int[]
     */
    protected function unserializeNumbers(string $rawNumbers): array
    {
        $rawNumbers = str_replace($this->delimiters, ',', $rawNumbers);

        return array_map('intval', explode(',', $rawNumbers));
    }

    /**
     * Check if there are negative numbers.
     *
     * @param int[] $numbers
     *
     * @throws NegativeNumbersAreNotAllowed
     */
    protected function guardAgainstNegativeNumbers(array $numbers): void
    {
        $negativeNumbers = array_filter($numbers, fn (int $n) => $n < 0);

        if (count($negativeNumbers) > 0) {
            throw new NegativeNumbersAreNotAllowed('negatives not allowed ' . implode(' ', $negativeNumbers));
        }
    }

    /**
     * Removes numbers bigger than 1000.
     *
     * @param int[] $numbers
     *
     * @return int[]
     */
    protected function removeYugeNumbers(array $numbers): array
    {
        return array_filter($numbers, fn (int $n) => $n <= 1000);
    }
}
