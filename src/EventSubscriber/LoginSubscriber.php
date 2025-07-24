<?php

namespace App\EventSubscriber;

use App\Entity\Session;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private RequestStack $requestStack;
    private EntityManagerInterface $em;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        $ip = $request->getClientIp();
        $agent = $request->headers->get('User-Agent');

        // Log simple (dans var/log/dev.log)
        $this->logger->info(sprintf("User '%s' logged in from %s - %s", $user->getUserIdentifier(), $ip, $agent));

        $sessionLogs = new Session();
        $sessionLogs->setUserId($user);
        $sessionLogs->setUserAgent($agent);
        $sessionLogs->setIp($ip);
        $sessionLogs->setLoggedAt(new \DateTimeImmutable());

        $this->em->persist($sessionLogs);
        $this->em->flush();
    }
}
