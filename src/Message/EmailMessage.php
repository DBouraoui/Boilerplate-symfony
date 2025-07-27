<?php

namespace App\Message;

readonly class EmailMessage
{
    public function __construct(
        public string $subject,
        public string $to,
        public string $template,
        public array  $context = [],
    ) {}
}
