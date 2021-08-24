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
}
