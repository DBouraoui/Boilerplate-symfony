<?php

namespace App\Service;

use App\DTO\UserRegisterDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {

    }

    public function createUser(UserRegisterDto $dto): User {

        if ( $this->entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]) ) {
            throw new \Exception("User already exists");
        }

        $user = new User();
        $user->setEmail($dto->email)
            ->setPassword($this->userPasswordHasher->hashPassword($user, $dto->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
