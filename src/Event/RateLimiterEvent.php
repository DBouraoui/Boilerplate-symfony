<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Custom event used to trigger a rate limiter on a specific endpoint.
 *
 * This event is designed to limit the number of allowed attempts for sensitive actions,
 * such as user registration, login, or API requests, using a unique identifier
 * (typically an IP address, email, or user ID).
 *
 * ✅ Meant to be dispatched before executing critical actions to protect against abuse,
 * spam, or brute-force attacks.
 *
 * Example usage:
 *     $event = new RateLimiterEvent($request->getClientIp());
 *     $eventDispatcher->dispatch($event);
 *
 * @author [DylanBro]
 */
class RateLimiterEvent extends Event
{
    /**
     * The unique identifier used for rate limiting (e.g. IP address, email, UUID...).
     * This value serves as the key under which the limiter tracks the request attempts.
     *
     * @var string
     */
    public ?string $idToLimited;

    /**
     * Constructor.
     *
     * @param string $idToLimited The identifier to apply rate limiting against.
     */
    public function __construct(string $idToLimited)
    {
        $this->idToLimited = $idToLimited;
    }

    /**
     * Returns the identifier used by the rate limiter.
     *
     * @return string
     */
    public function getIdToLimited(): string
    {
        return $this->idToLimited;
    }
}

