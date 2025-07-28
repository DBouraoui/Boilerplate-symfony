<?php

namespace App\Tests\Trait;

trait UserTestTrait
{
    public function createUser($client, ?string $email = "testuser@example.com",?string $password = "SecurePass123!"): void
    {
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => $password
            ])
        );
    }

    function randomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

}
