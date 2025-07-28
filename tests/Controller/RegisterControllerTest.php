<?php

namespace App\Tests\Controller;

use App\Tests\Trait\UserTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

final class RegisterControllerTest extends WebTestCase
{
    use UserTestTrait;
    protected $client;
    protected function setUp(): void
    {

        $this->client = RegisterControllerTest::createClient();

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    public function testUserCanRegister(): void
    {
        $this->createUser($this->client);

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);
    }

    public function testCreateUserWithEmailAlreadyExists(): void
    {
        $this->createUser($this->client);

        self::assertResponseStatusCodeSame(201);

        $this->createUser($this->client);
        self::assertResponseStatusCodeSame(500);
    }

    public function testCreateUserWithEmptyEmail(): void
    {
        $this->createUser($this->client,"");

        self::assertResponseStatusCodeSame(500);
    }

    public function testCreateUserWithEmptyPassword(): void
    {
        $this->createUser($this->client,"testuser@example.com", "");

        self::assertResponseStatusCodeSame(500);
    }

    public function testCreateUserWithEmptyPayload(): void
    {
        $this->createUser($this->client,"", "");

        self::assertResponseStatusCodeSame(500);
    }

    public function testCreateUserWithTooLongEmail(): void
    {
        $this->createUser($this->client,$this->randomString(190));

        self::assertResponseStatusCodeSame(500);
    }

    public function testCreateUserWithTooLongPassword(): void
    {
        $this->createUser($this->client,"testuser@example.com", $this->randomString(256));

        self::assertResponseStatusCodeSame(500);
    }
}
