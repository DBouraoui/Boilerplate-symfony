<?php

namespace App\Controller\Auth;

use App\DTO\UpdatePasswordDto;
use App\Service\AuthService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UpdatePasswordController extends AbstractController
{
    public function __construct(
        private AuthService $userService,
        private UtilitaireService $utilitaireService
    ){}

    #[Route(path: '/api/update-password', name: 'app_updatePassword', methods: ['PATCH'])]
    public function index(Request $request) {
        try {
            $data = json_decode($request->getContent());

            $updatePasswordDto = $this->utilitaireService->mapAndValidateRequestDto($data,new UpdatePasswordDto());

            $this->userService->updatePassword($updatePasswordDto);

            return $this->json('success', Response::HTTP_OK);
        } catch(\Throwable $throwable) {

            return $this->json(['error' => $throwable->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
