<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Trait\UserTestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ConfirmEmailControllerTest extends WebTestCase
{
    use UserTestTrait;
    protected ?KernelBrowser $client;
    protected ?EntityManagerInterface $entityManager;

    public string $email = "tes@test.com";
    public string $password = "password";

    public const CONFIRM_EMAIL_PATH = '/api/confirm-email';

    protected function setUp(): void
    {

        $this->client = ConfirmEmailControllerTest::createClient();

        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    public function testConfirmEmailSuccess(): void
    {
        $this->createUser($this->client, $this->email, $this->password);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'GET',
            self::CONFIRM_EMAIL_PATH,
            [
                'token' => $token,
                'email' => $this->email,
            ],
        );

        $this->assertResponseIsSuccessful();
    }

    public function testConfirmEmailErrorEmail(): void
    {
        $this->createUser($this->client, $this->email, $this->password);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'GET',
            self::CONFIRM_EMAIL_PATH,
            [
                'token' => $token,
                'email' => $this->email."dd",
            ],
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testConfirmEmailErrorToken(): void
    {
        $this->createUser($this->client, $this->email, $this->password);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'GET',
            self::CONFIRM_EMAIL_PATH,
            [
                'token' => $token."dede",
                'email' => $this->email,
            ],
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testConfirmEmailEmptyEmail(): void
    {
        $this->createUser($this->client, $this->email, $this->password);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'GET',
            self::CONFIRM_EMAIL_PATH,
            [
                'token' => $token."dede",
            ],
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testConfirmEmailEmptyToken(): void
    {
        $this->createUser($this->client, $this->email, $this->password);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);

        $user->getUserToken()->getToken();

        $this->client->request(
            'GET',
            self::CONFIRM_EMAIL_PATH,
            [
                'email' => $this->email,
            ],
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testConfirmEmailEmptyParams(): void
    {
        $this->createUser($this->client, $this->email, $this->password);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);

        $user->getUserToken()->getToken();

        $this->client->request(
            'GET',
            self::CONFIRM_EMAIL_PATH,
        );

        $this->assertResponseStatusCodeSame(400);
    }
}
