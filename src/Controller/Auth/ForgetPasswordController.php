<?php

namespace App\Controller\Auth;

use App\DTO\UserForgetPasswordDto;
use App\Service\AuthService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handle forget password requests by sending a reset token.
 */
final class ForgetPasswordController extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly AuthService $authService
    ) {}

    #[Route(path: '/api/forget-password', name: 'forget-password', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent());

            /** @var UserForgetPasswordDto $dto */
            $dto = $this->utilitaireService->mapAndValidateRequestDto($data, new UserForgetPasswordDto());

            $this->authService->forgetPassword($dto);

            return new JsonResponse(['message' => 'Password reset email sent.'], Response::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
