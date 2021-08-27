<?php

namespace Tests\Unit\Kata\StringCalculator;

use App\Events\AddOccurred;
use App\Exceptions\NegativeNumbersAreNotAllowed;
use Kata\StringCalculator\StringCalculator;
use Tests\TestCase;

class StringCalculatorTest extends TestCase
{
    /** @test */
    public function empty_string_should_equal_zero()
    {
        $this->assertEquals(0, (new StringCalculator())->add(''));
    }

    /** @test */
    public function single_number_should_return_its_own_value()
    {
        $number = '1';
        $this->assertEquals($number, (new StringCalculator())->add($number));
    }

    /** @test */
    public function two_numbers_should_return_their_sum()
    {
        $this->assertEquals(3, (new StringCalculator())->add('1,2'));
    }

    /** @test */
    public function multiple_numbers_should_return_their_sum()
    {
        $this->assertEquals(6, (new StringCalculator())->add('1,2,3'));
    }

    /** @test */
    public function can_handle_new_lines_as_delimiter()
    {
        $this->assertEquals(6, (new StringCalculator())->add('1\n2,3'));
        $this->assertEquals(6, (new StringCalculator())->add('1\n2\n3'));
    }

    /** @test */
    public function can_handle_a_specific_delimiter()
    {
        $this->assertEquals(3, (new StringCalculator())->add('//;\n1;2'));
    }

    /** @test */
    public function negative_number_is_not_allowed()
    {
        $this->expectException(NegativeNumbersAreNotAllowed::class);
        $this->expectExceptionMessage('negatives not allowed -1');
        (new StringCalculator())->add('-1,2,3');
    }

    /** @test */
    public function negative_numbers_are_not_allowed()
    {
        $this->expectException(NegativeNumbersAreNotAllowed::class);
        $this->expectExceptionMessage('negatives not allowed -1 -2 -3');
        (new StringCalculator())->add('-1,-2,-3');
    }

    /** @test */
    public function can_count_calls_to_add()
    {
        $stringCalculator = new StringCalculator();
        $stringCalculator->add('1,2,3');
        $stringCalculator->add('1,2,3');
        $stringCalculator->add('1,2,3');
        $this->assertEquals(3, $stringCalculator->getCalledCount());
    }

    /** @test */
    public function adding_should_trigger_add_occurred_event()
    {
        $this->expectsEvents(AddOccurred::class);
        (new StringCalculator())->add('1,2');
    }

    /** @test */
    public function numbers_bigger_than_1000_are_ignored()
    {
        $this->assertEquals(1, (new StringCalculator())->add('1,1001'));
        $this->assertEquals(1001, (new StringCalculator())->add('1,1000,1001'));
    }
}
