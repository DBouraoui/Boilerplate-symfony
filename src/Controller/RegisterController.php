<?php

namespace App\Controller;

use App\DTO\UserRegisterDto;
use App\Service\UserService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{

    public function __construct(
        private UserService $userService,
        private UtilitaireService $utilitaireService,
    ) {}

    #[Route('/api/register', name: 'app_register', methods: ['POST'],)]
    public function index(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent());

           $userRegisterDto = $this->utilitaireService->mapAndValidateRequestDto($data, new UserRegisterDto());

           $user = $this->userService->createUser($userRegisterDto);

            return $this->json($user, Response::HTTP_CREATED);
        } catch(\Throwable $throwable) {
            return $this->json(['error' => $throwable->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/test', name: 'app_regis', methods: ['GET'],)]
    public function test(Request $request): JsonResponse
    {
        try {
            $data = $request->getSession();

            dd($data);
        } catch(\Throwable $throwable) {
            return $this->json(['error' => $throwable->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
