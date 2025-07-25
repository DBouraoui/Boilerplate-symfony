<?php

namespace App\EventSubscriber;

use App\Entity\Session;
use App\Entity\User;
use App\Event\RateLimiterEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Subscriber that listens for user login events and logs useful session information.
 *
 * This subscriber listens to Symfony's LoginSuccessEvent, which is dispatched after a successful
 * user authentication. It logs the login details (IP address, User-Agent, timestamp) to the logger
 * and optionally persists them to the database for audit or security tracking.
 *
 * ✅ Automatically triggered after login when using Symfony's authenticator-based security system.
 *
 * Example data logged:
 * - User email or ID
 * - IP address
 * - User agent (browser/device)
 * - Login timestamp
 *
 * @author [DylanBro]
 */
readonly class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher
    ) {}

    /**
     * Returns the events this subscriber listens to.
     *
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    /**
     * Handles the login success event.
     *
     * Logs and persists user session data such as IP address and user agent.
     *
     * @param LoginSuccessEvent $event The login event triggered by Symfony
     */
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        $event = new RateLimiterEvent($user->getUserIdentifier());
        $this->dispatcher->dispatch($event);

        if (!$user instanceof User) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        $ipAddress = $request?->getClientIp() ?? 'unknown';
        $userAgent = $request?->headers->get('User-Agent') ?? 'unknown';

        // Log to file
        $this->logger->info(sprintf(
            "User '%s' logged in from IP %s - %s",
            $user->getUserIdentifier(),
            $ipAddress,
            $userAgent
        ));

        // Persist session log to database
        $session = new Session();
        $session->setUserId($user);
        $session->setIp($ipAddress);
        $session->setUserAgent($userAgent);
        $session->setLoggedAt(new \DateTimeImmutable());

        $this->entityManager->persist($session);
        $this->entityManager->flush();
    }
}
