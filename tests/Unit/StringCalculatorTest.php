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
}
