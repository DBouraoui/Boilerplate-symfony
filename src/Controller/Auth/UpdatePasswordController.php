<?php

namespace App\Controller\Auth;

use App\DTO\UpdatePasswordDto;
use App\Service\AuthService;
use App\Service\UtilitaireService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles user password update requests via token.
 */
class UpdatePasswordController extends AbstractController
{
    public function __construct(
        private AuthService       $userService,
        private UtilitaireService $utilitaireService, private readonly LoggerInterface $logger
    ) {}

    /**
     * Updates the user's password based on a valid token and the new password provided.
     *
     * @param Request $request The HTTP request containing the token and new password.
     * @return Response A JSON response indicating success or failure.
     */
    #[Route(path: '/api/update-password', name: 'app_updatePassword', methods: ['PATCH'])]
    public function __invoke(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent());

            // Map and validate request data to a DTO
            $updatePasswordDto = $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new UpdatePasswordDto()
            );

            // Process password update
           $user = $this->userService->updatePassword($updatePasswordDto);

           $this->logger->log(1,sprintf("%s update password",$user->getEmail()));

            return $this->json('success', Response::HTTP_OK);

        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $throwable->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
