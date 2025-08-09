<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Trait\UserTestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UpdatePasswordControllerTest extends WebTestCase
{
    use UserTestTrait;

    protected ?KernelBrowser $client;
    protected ?EntityManagerInterface $entityManager;

    public string $email = "tes@test.com";
    public string $password = "password";
    public string $newPassword = "newPassword";

    public const UPDATE_PASSWORD_PATH = '/api/update-password';
    public const FORGET_PASSWORD_PATH = '/api/forget-password';

    protected function setUp(): void
    {

        $this->client = UpdatePasswordControllerTest::createClient();

        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    public function testConfirmEmailSuccess(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->activateUserAccount($this->email);

        $this->client->request(
            'POST',
            self::FORGET_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'email'=>$this->email
            ])
        );

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email'=>$this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'PATCH',
                self::UPDATE_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'password' => $this->newPassword,
                'token'=>$token
            ])
        );

        $this->assertResponseIsSuccessful();

        $this->connexionUser($this->client,$this->email, $this->newPassword);

        $this->assertResponseIsSuccessful();
    }

    public function testConfirmEmailBadToken(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->activateUserAccount($this->email);

        $this->client->request(
            'POST',
            self::FORGET_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'email'=>$this->email
            ])
        );

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email'=>$this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'PATCH',
            self::UPDATE_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'password' => $this->newPassword,
                'token'=>$token."defef"
            ])
        );

        $this->assertResponseStatusCodeSame(500);
    }

    public function testConfirmEmailBadConnexionPassword(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->activateUserAccount($this->email);

        $this->client->request(
            'POST',
            self::FORGET_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'email'=>$this->email
            ])
        );

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email'=>$this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'PATCH',
            self::UPDATE_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'password' => $this->newPassword,
                'token'=>$token
            ])
        );


        $this->assertResponseIsSuccessful();

        $this->connexionUser($this->client,$this->email, $this->password);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testConfirmEmailPasswordToLong(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->activateUserAccount($this->email);

        $this->client->request(
            'POST',
            self::FORGET_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'email'=>$this->email
            ])
        );

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email'=>$this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'PATCH',
            self::UPDATE_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'password' => $this->randomString(260),
                'token'=>$token
            ])
        );

        $this->assertResponseStatusCodeSame(500);
    }

    public function testConfirmEmailPasswordToShort(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->activateUserAccount($this->email);

        $this->client->request(
            'POST',
            self::FORGET_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'email'=>$this->email
            ])
        );

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email'=>$this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'PATCH',
            self::UPDATE_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'password' => $this->randomString(6),
                'token'=>$token
            ])
        );

        $this->assertResponseStatusCodeSame(500);
    }

    public function testConfirmEmailEmptyPassword(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->activateUserAccount($this->email);

        $this->client->request(
            'POST',
            self::FORGET_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'email'=>$this->email
            ])
        );

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email'=>$this->email]);

        $token = $user->getUserToken()->getToken();

        $this->client->request(
            'PATCH',
            self::UPDATE_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'password' => '',
                'token'=>$token
            ])
        );

        $this->assertResponseStatusCodeSame(500);
    }

}
