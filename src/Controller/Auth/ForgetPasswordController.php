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

class ForgetPasswordController extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly AuthService $userService
    ){}

    #[Route(path: '/api/forget-password', name: 'forget-password', methods: ['POST'])]
    public function index(Request $request) {
        try {
            $data = json_decode($request->getContent());

           $userForgetPasswordDto = $this->utilitaireService->mapAndValidateRequestDto($data, new UserForgetPasswordDto());

           $this->userService->forgetPassword($userForgetPasswordDto);

           return new JsonResponse(["success" => true], Response::HTTP_OK);

        } catch (\Throwable $throwable) {

            return new JsonResponse(['error' => $throwable->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
