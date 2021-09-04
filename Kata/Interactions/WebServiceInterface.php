<?php

declare(strict_types=1);

namespace Kata\Interactions;

interface WebServiceInterface
{
    public function notify(string $message): void;
}
