<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class EmailEvent extends Event
{
    public const NAME = 'email';
    public ?string $subject;
    public ?string $to;
    public ?string $template;
    public ?array $context;

    public function __construct(
        string $subject,
        string $to,
        string $template,
        array $context,
    ) {
        $this->subject = $subject;
        $this->to = $to;
        $this->template = $template;
        $this->context = $context;
    }
}
