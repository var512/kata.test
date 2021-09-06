<?php

namespace Tests\Unit\Kata\Interactions;

use App\Events\AddOccurred;
use App\Exceptions\NegativeNumbersNotAllowedException;
use Exception;
use Illuminate\Support\Facades\Log;
use Kata\Interactions\StringCalculator;
use Kata\Interactions\WebServiceInterface;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class StringCalculatorTest extends TestCase
{
    private StringCalculator $stringCalculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stringCalculator = app()->make(StringCalculator::class);
    }

    /** @test */
    public function empty_string_should_equal_zero(): void
    {
        $this->assertSame(0, $this->stringCalculator->add(''));
    }

    /** @test */
    public function single_number_should_return_its_own_value(): void
    {
        $this->assertSame(1, $this->stringCalculator->add('1'));
    }

    /** @test */
    public function two_numbers_should_return_their_sum(): void
    {
        $this->assertSame(3, $this->stringCalculator->add('1,2'));
    }

    /** @test */
    public function multiple_numbers_should_return_their_sum(): void
    {
        $this->assertSame(6, $this->stringCalculator->add('1,2,3'));
    }

    /** @test */
    public function can_handle_new_lines_as_delimiter(): void
    {
        $this->assertSame(6, $this->stringCalculator->add('1\n2,3'));
        $this->assertSame(6, $this->stringCalculator->add('1\n2\n3'));
    }

    /** @test */
    public function can_handle_a_specific_delimiter(): void
    {
        $this->assertSame(3, $this->stringCalculator->add('//;\n1;2'));
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
        $this->assertSame(3, $this->stringCalculator->getCalledCount());
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
        $this->assertSame(1, $this->stringCalculator->add('1,1001'));
        $this->assertSame(1001, $this->stringCalculator->add('1,1000,1001'));
    }

    /** @test */
    public function delimiters_can_be_of_any_length(): void
    {
        $this->assertSame(6, $this->stringCalculator->add('//[***]\n1***2***3'));
    }

    /** @test */
    public function allow_multiple_delimiters(): void
    {
        $this->assertSame(6, $this->stringCalculator->add('//[*][%]\n1*2%3'));
    }

    /** @test */
    public function allow_multiple_delimiters_with_any_length(): void
    {
        $this->assertSame(6, $this->stringCalculator->add('//[**][%%]\n1**2%%3'));
    }

    /** @test */
    public function sum_results_should_be_logged(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->withArgs(fn ($message) => $message === '3');

        $this->stringCalculator->add('1,2');
    }

    /** @test */
    public function sum_logging_exceptions_should_notify_somewebservice(): void
    {
        Log::swap(new class {
            public static function info(string $message): void
            {
                throw new Exception('log fail ' . $message);
            }
        });

        $this->instance(
            WebServiceInterface::class,
            Mockery::mock(WebServiceInterface::class, function (MockInterface $mock) {
                $mock->shouldReceive('notify')->once()->with('log fail 3');
            })
        );

        app()->make(StringCalculator::class)->add('1,2');
    }
}