<?php

namespace App\Exceptions;

use Exception;

class NegativeNumbersNotAllowedException extends Exception
{
    /**
     * @param int[] $negativeNumbers
     */
    public function __construct(array $negativeNumbers)
    {
        parent::__construct('negatives not allowed ' . implode(' ', $negativeNumbers));
    }
}
