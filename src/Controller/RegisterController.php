<?php

namespace App\Controller;

use App\DTO\UserRegisterDto;
use App\Event\RateLimiterEvent;
use App\Service\UserService;
use App\Service\UtilitaireService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller responsible for handling user registration.
 *
 * This controller applies a rate limiter to prevent abuse (e.g. spam or bot signups),
 * maps and validates the incoming registration data, and delegates user creation
 * to the UserService.
 *
 * If the rate limit is exceeded or any error occurs during processing, an appropriate
 * error response is returned.
 *
 * @author [DylanBro]
 */
final class RegisterController extends AbstractController
{
    /**
     * @param UserService $userService Handles user creation logic
     * @param UtilitaireService $utilitaireService Utility to map/validate DTOs
     * @param EventDispatcherInterface $eventDispatcher Dispatches events (e.g. rate limiter)
     */
    public function __construct(
        private UserService $userService,
        private UtilitaireService $utilitaireService,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * Handles the user registration endpoint.
     *
     * This route accepts a POST request with user registration data in JSON format,
     * applies rate limiting based on the client's IP address, and creates a new user.
     *
     * @param Request $request The HTTP request containing registration data
     *
     * @return JsonResponse The created user on success or an error response
     */
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $this->eventDispatcher->dispatch(
                new RateLimiterEvent($request->getClientIp())
            );

            $data = json_decode($request->getContent());

            $userRegisterDto = $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new UserRegisterDto()
            );

            $user = $this->userService->createUser($userRegisterDto);

            return $this->json($user, Response::HTTP_CREATED);
        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $throwable->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
