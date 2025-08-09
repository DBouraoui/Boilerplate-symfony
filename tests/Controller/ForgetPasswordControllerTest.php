<?php

namespace App\Tests\Controller;

use App\Tests\Trait\UserTestTrait;
use Doctrine\ORM\Tools\SchemaTool;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ForgetPasswordControllerTest extends WebTestCase
{
    use UserTestTrait;
    protected $client;

    public string $email = "tes@test.com";
    public string $password = "password";
    public const FORGET_PASSWORD_PATH = '/api/forget-password';

    protected function setUp(): void
    {

        $this->client = ForgetPasswordControllerTest::createClient();

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    public function testSendingEmailForgetPasswordSuccess(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->activateUserAccount($this->email);
        $this->connexionUserAndReturnToken($this->client, $this->email, $this->password);

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

        $this->assertResponseIsSuccessful();
    }

    public function testSendingEmailForgetPasswordWithAccountNotActivated(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->connexionUserAndReturnToken($this->client, $this->email, $this->password);

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

        $this->assertResponseStatusCodeSame(500);
    }

    public function testSendingEmailForgetPasswordWithBadPayload(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->connexionUserAndReturnToken($this->client, $this->email, $this->password);

        $this->client->request(
            'POST',
            self::FORGET_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'email'=>$this->randomString()
            ])
        );

        $this->assertResponseStatusCodeSame(500);
    }

    public function testSendingEmailForgetPasswordWithEmptyPayload(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->connexionUserAndReturnToken($this->client, $this->email, $this->password);

        $this->client->request(
            'POST',
            self::FORGET_PASSWORD_PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'email'=>''
            ])
        );

        $this->assertResponseStatusCodeSame(500);
    }
}
