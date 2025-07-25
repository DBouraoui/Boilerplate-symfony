<?php

namespace App\Message;

class EmailMessage
{
    public function __construct(
        public readonly string $subject,
        public readonly string $to,
        public readonly string $template,
        public readonly array $context = [],
    ) {}
}
