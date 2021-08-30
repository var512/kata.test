<?php

namespace Tests\Unit\Kata\StringCalculator;

use App\Events\AddOccurred;
use App\Exceptions\NegativeNumbersNotAllowedException;
use Kata\StringCalculator\StringCalculator;
use Tests\TestCase;

class StringCalculatorTest extends TestCase
{
    private StringCalculator $stringCalculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stringCalculator = new StringCalculator();
    }

    /** @test */
    public function empty_string_should_equal_zero(): void
    {
        $this->assertEquals(0, $this->stringCalculator->add(''));
    }

    /** @test */
    public function single_number_should_return_its_own_value(): void
    {
        $number = '1';
        $this->assertEquals($number, $this->stringCalculator->add($number));
    }

    /** @test */
    public function two_numbers_should_return_their_sum(): void
    {
        $this->assertEquals(3, $this->stringCalculator->add('1,2'));
    }

    /** @test */
    public function multiple_numbers_should_return_their_sum(): void
    {
        $this->assertEquals(6, $this->stringCalculator->add('1,2,3'));
    }

    /** @test */
    public function can_handle_new_lines_as_delimiter(): void
    {
        $this->assertEquals(6, $this->stringCalculator->add('1\n2,3'));
        $this->assertEquals(6, $this->stringCalculator->add('1\n2\n3'));
    }

    /** @test */
    public function can_handle_a_specific_delimiter(): void
    {
        $this->assertEquals(3, $this->stringCalculator->add('//;\n1;2'));
    }

    /** @test */
    public function negative_number_is_not_allowed(): void
    {
        $this->expectException(NegativeNumbersNotAllowedException::class);
        $this->expectExceptionMessage('negatives not allowed -1');
        $this->stringCalculator->add('-1,2,3');
    }

    /** @test */
    public function negative_numbers_are_not_allowed(): void
    {
        $this->expectException(NegativeNumbersNotAllowedException::class);
        $this->expectExceptionMessage('negatives not allowed -1 -2 -3');
        $this->stringCalculator->add('-1,-2,-3');
    }

    /** @test */
    public function can_count_calls_to_add(): void
    {
        $this->stringCalculator->add('');
        $this->stringCalculator->add('0');
        $this->stringCalculator->add('1,2');
        $this->assertEquals(3, $this->stringCalculator->getCalledCount());
    }

    /** @test */
    public function adding_should_trigger_add_occurred_event(): void
    {
        $this->expectsEvents(AddOccurred::class);
        $this->stringCalculator->add('1,2');
    }

    /** @test */
    public function numbers_bigger_than_1000_are_ignored(): void
    {
        $this->assertEquals(1, $this->stringCalculator->add('1,1001'));
        $this->assertEquals(1001, $this->stringCalculator->add('1,1000,1001'));
    }

    /** @test */
    public function delimiters_can_be_of_any_length(): void
    {
        $this->assertEquals(6, $this->stringCalculator->add('//[***]\n1***2***3'));
    }

    /** @test */
    public function allow_multiple_delimiters(): void
    {
        $this->assertEquals(6, $this->stringCalculator->add('//[*][%]\n1*2%3'));
    }

    /** @test */
    public function allow_multiple_delimiters_with_any_length(): void
    {
        $this->assertEquals(6, $this->stringCalculator->add('//[**][%%]\n1**2%%3'));
    }
}
