<?php

namespace App\Tests\Trait;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

trait UserTestTrait
{
    public ?EntityManagerInterface $em;
    public const REGISTER_PATH = "/api/register";
    public const LOGIN_PATH = "/api/login_check";


    public function createUser($client, ?string $email = "testuser@example.com",?string $password = "SecurePass123!"): void
    {
        $client->request(
            'POST',
            self::REGISTER_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => $password
            ])
        );
    }


    public function connexionUserAndReturnToken($client, $email, $password): string
    {
        $this->connexionUser($client, $email, $password);

        $response = $client->getResponse();

        $data = json_decode($response->getContent(), true);

        if (isset($data['token'])) {
            $this->assertArrayHasKey('token', $data);
        }

        return $data['token'] ?? 'token' ;
    }

    public function connexionWithActivationUser($client, string $email, string $password) : void
    {
        $this->activateUserAccount($email);

        $this->connexionUser($client, $email, $password);
    }


    public function connexionUser($client, $email, $password): void
    {
        $client->request(
            'POST',
            self::LOGIN_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => $password
            ])
        );
    }

    private function activateUserAccount(string $email) : User {
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        $token = $user->getUserToken();

        $user->setIsActive(true);

        $this->em->persist($user);
        $this->em->remove($token);
        $this->em->flush();

        return $user;
    }

    function randomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

}
