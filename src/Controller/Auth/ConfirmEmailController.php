<?php

namespace App\Controller\Auth;

use App\Event\EmailEvent;
use App\Service\AuthService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class ConfirmEmailController
{
    const EMAIL_TITLE = "Your email is confirmed !";
    const EMAIL_TEMPLATE = "ConfirmEmail.html.twig";

    public function __construct(private readonly AuthService              $userService,
                                private readonly EventDispatcherInterface $eventDispatcher
    ) {}

    #[Route(path:"/api/confirm-email",name: 'confirm-email', methods: ['GET'])]
    public function index(Request $request) {
        try {
            $token = $request->query->get('token');
            $emailAddress = $request->query->get('email');

            if (empty($token) || empty($emailAddress)) {
                throw new \Exception("Missing token or parameter 'token' or 'email'.");
            }

           $user = $this->userService->confirmEmail($token, $emailAddress);

            $email = new EmailEvent(
                self::EMAIL_TITLE,
                $user->getEmail(),
                self::EMAIL_TEMPLATE,
                [
                    "user"=>$user
                ]
            );

            $this->eventDispatcher->dispatch(
                $email
            );

            return new JsonResponse(["success"],Response::HTTP_OK);
        } catch (\Throwable $throwable) {

            throw new BadRequestHttpException($throwable->getMessage());
        }
    }
}
