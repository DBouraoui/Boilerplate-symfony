<?php

namespace App\MessageHandler;

use App\Message\EmailMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EmailMessageHandler
{
    public function __construct(
        private MailerInterface $mailer,
    ) {}

    public function __invoke(EmailMessage $message): void
    {
        $email = (new TemplatedEmail())
            ->from($_SERVER['EMAIL_ADDRESS'])
            ->to($message->to)
            ->subject($message->subject)
            ->htmlTemplate('email/' . $message->template . '.html.twig')
            ->context($message->context);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Failed to send async email: ' . $e->getMessage());
        }
    }
}
