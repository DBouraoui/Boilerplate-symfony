<?php

namespace App\Tests\Controller;

use App\Tests\Trait\UserTestTrait;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ConnexionControllerTest extends WebTestCase
{
    use UserTestTrait;
    protected $client;

    public string $email = "tes@test.com";
    public string $password = "password";

    protected function setUp(): void
    {

        $this->client = ConnexionControllerTest::createClient();

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    public function testConnexionUserActivated(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->connexionWithActivationUser($this->client, $this->email, $this->password);

        $this->assertResponseIsSuccessful();
    }

    public function testConnexionUserNotActivated(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->connexionUser($this->client, $this->email, $this->password);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testConnexionBadEmail(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->connexionUser($this->client, $this->randomString(), $this->password);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testConnexionBadPassword(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->connexionUser($this->client, $this->email, $this->randomString());

        $this->assertResponseStatusCodeSame(401);
    }

    public function testConnexionEmptyCredentials(): void
    {
        $this->createUser($this->client, $this->email, $this->password);
        $this->connexionUser($this->client, "", "");

        $this->assertResponseStatusCodeSame(400);
    }
}
