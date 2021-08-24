<?php

namespace Tests\Unit;

use Domain\StringCalculator;
use PHPUnit\Framework\TestCase;

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
        $this->assertEquals(-1, (new StringCalculator())->add('0,1,-1,-1'));
        $this->assertEquals(0, (new StringCalculator())->add('0,0,0'));
    }

    /** @test */
    public function can_handle_new_lines_as_separator()
    {
        $this->assertEquals(6, (new StringCalculator())->add('1\n2,3'));
        $this->assertEquals(6, (new StringCalculator())->add('1\n2\n3'));
    }
}
