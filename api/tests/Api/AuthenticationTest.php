<?php

namespace App\Tests\Api;

use App\Entity\User;

class AuthenticationTest extends AbstractTest
{
    private const LOGIN_ENDPOINT = "/api/login";

    public function testLogin()
    {
        $response = self::createClientForRole(null)->request('POST', self::LOGIN_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'admin@dev.fr',
                'password' => 'admin',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $response->toArray());
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testLoginFailedIfUserNotRegistered()
    {
        $response = self::createClientForRole(null)->request('POST', self::LOGIN_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'not-registered@dev.fr',
                'password' => 'not-registered',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}