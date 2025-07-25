<?php

namespace App\Service;

use App\DTO\UserRegisterDto;
use App\Entity\User;
use App\Entity\UserToken;
use App\Enum\TokenType;
use App\Interface\DtoInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

readonly class AuthService
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private UtilitaireService $utilitaireService
    ) {

    }

    public function createUser(DtoInterface $dto): User {

        if ( $this->entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]) ) {
            throw new \Exception("User already exists");
        }

        $now = new \DateTimeImmutable();

        $user = (new User());
        $user->setEmail($dto->email)
            ->setPassword($this->userPasswordHasher->hashPassword($user, $dto->password))
            ->setCreatedAt($now)
            ->setUpdatedAt($now);

        $token = (new UserToken())
            ->setType(TokenType::REGISTER)
            ->setCreatedAt($now)
            ->setExpiredAt($now->modify('+2 hours'))
            ->setToken(Uuid::v4())
            ->setRelatedUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $user;
    }

    public function confirmEmail(string $token, string $email): User {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (empty($user)) {
            throw new \Exception("User not found");
        }

        if ($user->getUserToken()->getToken()->toRfc4122() !== $token) {
            throw new \Exception("User not found");
        }

        if ($user->getUserToken()->getType() !== TokenType::REGISTER) {
            throw new \Exception("User not found");
        }

        $this->entityManager->remove($user->getUserToken());
        $this->entityManager->flush();

        return $user;
    }

    public function forgetPassword(DtoInterface $dto): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]);

        if (!$user) {
            throw new \Exception("User not found");
        }

        $now = new \DateTimeImmutable();
        $token = $user->getUserToken();

        if ($token) {
            if ($token->getType() === TokenType::FORGET_PASSWORD && $token->getExpiredAt() < $now) {
                $token = $this->updateTokenUser($token, $now);
                $this->sendForgetPasswordEmail($user, $token, "TokenRefresh.html.twig");
                return;
            }

            if ($token->getType() !== TokenType::FORGET_PASSWORD) {
                throw new \Exception("A different token is already active for this user.");
            }

            $this->entityManager->remove($token);
            $this->entityManager->flush();
        }

        $token = $this->createTokenUser($user, $now);
        $this->sendForgetPasswordEmail($user, $token, "ForgetPassword.html.twig");
    }

    public function updatePassword(DtoInterface $dto): User
    {
        $userTokenModel = $this->entityManager->getRepository(UserToken::class)->findOneBy(['token'=>$dto->token]);

        if (empty($userTokenModel)) {
            throw new \Exception("User not found");
        }

        $user = $userTokenModel->getRelatedUser();

        $userUpdate = $this->updatePasswordUser($user, $dto);

        $this->deleteTokenUser($userTokenModel);

        $this->utilitaireService->sendEmail(
            "Your email was changed",
            $userUpdate->getEmail(),
            "UpdatePassword.html.twig",
            [
                "user" => $user,
            ]
        );

        return $user;
    }

    private function createTokenUser(User $user, \DateTimeImmutable $now): UserToken
    {
        $userToken = new UserToken();
        $userToken->setToken(Uuid::v4());
        $userToken->setType(TokenType::FORGET_PASSWORD);
        $userToken->setCreatedAt($now);
        $userToken->setExpiredAt($now->modify('+2 hours'));
        $userToken->setRelatedUser($user);

        $this->entityManager->persist($userToken);
        $this->entityManager->flush();

        return $userToken;
    }

    private function updateTokenUser(UserToken $userToken, \DateTimeImmutable $now): UserToken {
        $userToken->setExpiredAt($now->modify('+2 hours'));
        $this->entityManager->persist($userToken);
        $this->entityManager->flush();

        return $userToken;
    }

    private function deleteTokenUser(UserToken $userToken): void
    {
        $this->entityManager->remove($userToken);
        $this->entityManager->flush();
    }

    private function sendForgetPasswordEmail(User $user, UserToken $token, string $template): void
    {
        $this->utilitaireService->sendEmail(
            "Change your password",
            $user->getEmail(),
            $template,
            [
                'user' => $user,
                'validate_link' => $_ENV['FRONT_URL'] . "/forget-password/" . $token->getToken(),
                'token_expiration' => $token->getExpiredAt(),
            ]
        );
    }

    private function updatePasswordUser(User $user, DtoInterface $dto): User {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $dto->password));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

}
