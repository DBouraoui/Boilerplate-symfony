<?php

namespace App\EventSubscriber;

use App\Event\RateLimiterEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

/**
 * Subscriber that handles rate limiting for specific user actions (e.g. registration, login).
 *
 * This subscriber listens to the RateLimiterEvent and applies rate limiting using Symfony's
 * RateLimiter component, based on a unique identifier (usually an IP address or email).
 *
 * If the rate limit is exceeded, an exception is thrown to prevent further processing.
 *
 * 💡 You can reuse this subscriber for multiple endpoints by dispatching the RateLimiterEvent
 * with different identifiers.
 *
 * @author [DylanBro]
 */
readonly class RateLimiterSubscriber implements EventSubscriberInterface
{
    /**
     * @param RateLimiterFactoryInterface $rateLimiterFactory The rate limiter factory
     * injected with the 'limiter.registration' service
     */
    public function __construct(
        #[Autowire(service: 'limiter.registration')]
        private RateLimiterFactoryInterface $rateLimiterFactory,
        private LoggerInterface $logger
    ) {}

    /**
     * Defines the event(s) this subscriber listens to.
     *
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RateLimiterEvent::class => 'onRateLimitCheck',
        ];
    }

    /**
     * Event handler that applies the rate limit.
     *
     * @param RateLimiterEvent $event The event containing the identifier to limit
     *
     * @throws \RuntimeException If the rate limit is exceeded
     */
    public function onRateLimitCheck(RateLimiterEvent $event): void
    {
        $identifier = $event->getIdToLimited();
        $limiter = $this->rateLimiterFactory->create($identifier);

        $limit = $limiter->consume();

        if (!$limit->isAccepted()) {
            $this->logger->warning(sprintf('Rate limit exceeded for identifier "%s".', $identifier));
            throw new \RuntimeException('Too many attempts. Please try again later.');
        }
    }
}

