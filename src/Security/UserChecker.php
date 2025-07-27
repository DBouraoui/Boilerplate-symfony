<?php

namespace App\Security;

use App\Entity\User;
use App\Enum\TokenType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        $this->checkUserStatus($user);
        $this->handleTokenState($user);
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Reserved for post-auth checks (e.g., MFA, recent password change, etc.)
    }

    private function checkUserStatus(User $user): void
    {
        if ($user->getDeletedAt() !== null || !$user->isActive()) {
            throw new CustomUserMessageAuthenticationException("Your account is not active.");
        }
    }

    private function handleTokenState(User $user): void
    {
        $token = $user->getUserToken();

        if (!$token) {
            return;
        }

        match ($token->getType()) {
            TokenType::REGISTER => throw new CustomUserMessageAuthenticationException("Your account is not verified."),
            TokenType::FORGET_PASSWORD => $this->deleteToken($user),
            default => null, // Do nothing for other token types
        };
    }

    private function deleteToken(User $user): void
    {
        $token = $user->getUserToken();

        if ($token) {
            $user->setUserToken(null);
            $this->entityManager->remove($token);
            $this->entityManager->flush();
        }
    }
}
