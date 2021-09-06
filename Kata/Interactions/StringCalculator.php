<?php

declare(strict_types=1);

namespace Kata\Interactions;

use App\Events\AddOccurred;
use App\Exceptions\InvalidMetadataException;
use App\Exceptions\NegativeNumbersNotAllowedException;
use Exception;
use Illuminate\Support\Facades\Log;

class StringCalculator
{
    /** @var string[] */
    private array $delimiters = [',', '\n'];

    private int $calledCount = 0;

    private WebServiceInterface $someWebService;

    public function __construct(WebServiceInterface $someWebService)
    {
        $this->someWebService = $someWebService;
    }

    /**
     * @throws NegativeNumbersNotAllowedException
     * @throws InvalidMetadataException
     */
    public function add(string $rawNumbers): int
    {
        AddOccurred::dispatch($this->calledCount++);

        if ($rawNumbers === '') {
            return 0;
        }

        $this->delimiters = array_merge($this->delimiters, $this->getCustomDelimiter($rawNumbers));

        $numbers = $this->unserializeNumbers(
            $this->removeMetadata($rawNumbers)
        );

        $this->guardAgainstNegativeNumbers($numbers);

        $numbers = $this->removeYugeNumbers($numbers);

        return $this->sum($numbers);
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
     * @return string[]
     */
    protected function getCustomDelimiter(string $rawNumbers): array
    {
        $customDelimiter = [];

        preg_match_all('/(?<multipleDelimiters>\[(.*?)])/', $rawNumbers, $customDelimiter);

        if (count($customDelimiter[0]) > 1) {
            return $customDelimiter[2];
        }

        preg_match(
            '/(?<specificDelimiter>^\/\/(.)\\\n(.*))|(?<anyLengthDelimiter>^\/\/\[(.*)]\\\n(.*))/',
            $rawNumbers,
            $customDelimiter,
        );

        $customDelimiterIndex = count($customDelimiter) === 9 ? 5 : 2;

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
     * @throws NegativeNumbersNotAllowedException
     */
    protected function guardAgainstNegativeNumbers(array $numbers): void
    {
        $negativeNumbers = array_filter($numbers, fn (int $n) => $n < 0);

        if (count($negativeNumbers) > 0) {
            throw new NegativeNumbersNotAllowedException($negativeNumbers);
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

    /**
     * Returns the sum of all numbers.
     *
     * @param int[] $numbers
     *
     * @return int
     */
    protected function sum(array $numbers): int
    {
        $sum = array_sum($numbers);

        try {
            Log::info((string) $sum);
        } catch (Exception $e) {
            $this->someWebService->notify($e->getMessage());
        }

        return $sum;
    }
}
