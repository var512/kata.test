<?php

declare(strict_types=1);

namespace Kata\Interactions;

interface SomewebserviceInterface
{
    public function notify(string $message): void;
}
