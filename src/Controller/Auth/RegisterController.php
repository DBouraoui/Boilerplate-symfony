<?php

namespace App\Controller\Auth;

use App\DTO\UserRegisterDto;
use App\Event\RateLimiterEvent;
use App\Service\AuthService;
use App\Service\UtilitaireService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles user registration.
 *
 * Applies rate limiting, maps and validates incoming data,
 * and delegates user creation to the AuthService.
 *
 * @author DylanBro
 */
final class RegisterController extends AbstractController
{
    public function __construct(
        private readonly AuthService              $authService,
        private readonly UtilitaireService        $utilService,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * User registration endpoint.
     *
     * Accepts user data, applies IP-based rate limit, validates input,
     * and creates the user account.
     */
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            // Trigger rate limiter to prevent abuse
            $this->dispatcher->dispatch(
                new RateLimiterEvent($request->getClientIp())
            );

            // Decode and validate the registration data
            $requestData = json_decode($request->getContent());
            $registerDto = $this->utilService->mapAndValidateRequestDto(
                $requestData,
                new UserRegisterDto()
            );

            // Create user and generate token
           $user =  $this->authService->createUser($registerDto);

            $this->logger->log(1,sprintf("%s register", $user->getEmail()));

            return $this->json('success', Response::HTTP_CREATED);

        } catch (\Throwable $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
