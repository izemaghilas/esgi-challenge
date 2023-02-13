<?php

namespace App\Tests\Api;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\Enums\Roles;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;


abstract class AbstractTest extends ApiTestCase
{
    private const AUTH_ENDPOINT = '/api/login';
    
    private const CREDENTIALS = [
        (Roles::ADMIN->value) => [
            'email' => 'admin@dev.fr',
            'password' => 'admin'
        ],
        (Roles::REVIEWER->value) => [
            'email' => 'reviewer1@dev.fr',
            'password' => 'reviewer'
        ],
        (Roles::CONTRIBUTOR->value) => [
            'email' => 'contributor1@dev.fr',
            'password' => 'contributor'
        ],
        (Roles::USER->value) => [
            'email' => 'user1@dev.fr',
            'password' => 'user'
        ],
    ];
    
    use RefreshDatabaseTrait;

    public function setUp(): void
    {
        self::bootKernel();
    }

    protected function createClientForRole(?string $role): ?Client
    {
        if(null === $role || null === self::CREDENTIALS[$role])
        {
            return static::createClient([], []);
        }
        $credentials = self::CREDENTIALS[$role];
        $token = $this->getToken(['email' => $credentials['email'], 'password' => $credentials['password']]);
        return static::createClient([], [
            'headers' => ['authorization' => 'Bearer ' . $token]
        ]);
    }

    protected function getToken(array $body): string
    {
        $response = static::createClient([], [])->request('POST', self::AUTH_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => $body['email'],
                'password' => $body['password']
            ]
        ]);
        $data = $response->toArray();
        return $data['token'];
    }
}