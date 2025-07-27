<?php

namespace App\Controller\Auth;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles email confirmation from a tokenized validation link.
 *
 * Example: /api/confirm-email?token=xxxx&email=example@domain.com
 */
final class ConfirmEmailController extends AbstractController
{
    public function __construct(
        private AuthService $authService,
    ) {}

    #[Route(path: '/api/confirm-email', name: 'confirm-email', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $token = $request->query->get('token');
            $email = $request->query->get('email');

            if (empty($token) || empty($email)) {
                throw new \InvalidArgumentException("Missing required parameters: 'token' and 'email'.");
            }

            $this->authService->confirmEmail($token, $email);

            return $this->json(['message' => 'Email successfully confirmed.'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
