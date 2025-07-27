<?php

namespace App\EventSubscriber;

use App\Event\EmailEvent;
use App\Message\EmailMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class EmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $bus,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            EmailEvent::class => 'onEmail',
        ];
    }

    public function onEmail(EmailEvent $event): void
    {
        $this->bus->dispatch(new EmailMessage(
            subject: $event->subject,
            to: $event->to,
            template: $event->template,
            context: $event->context ?? []
        ));
    }
}
