<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Enums\Role;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;


abstract class AbstractTest extends ApiTestCase
{
    private const AUTH_ENDPOINT = '/api/login';

    private const CREDENTIALS = [
        (Role::ADMIN->value) => [
            'email' => 'admin@dev.fr',
            'password' => 'admin'
        ],
        (Role::REVIEWER->value) => [
            'email' => 'reviewer1@dev.fr',
            'password' => 'reviewer'
        ],
        (Role::CONTRIBUTOR->value) => [
            'email' => 'contributor1@dev.fr',
            'password' => 'contributor'
        ],
        (Role::USER->value) => [
            'email' => 'user1@dev.fr',
            'password' => 'user'
        ],
    ];

    private Client $client;

    use RefreshDatabaseTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->client = self::createClient([], []);
        $this->client->disableReboot();
    }


    protected function createClientForRole(?string $role = null): ?Client
    {
        if (null === $role || null === self::CREDENTIALS[$role]) {
            $this->client->setDefaultOptions([]);
        } else {
            $credentials = self::CREDENTIALS[$role];
            $token = $this->getToken(['email' => $credentials['email'], 'password' => $credentials['password']]);
            $this->client->setDefaultOptions([
                'headers' => ['authorization' => 'Bearer ' . $token]
            ]);
        }

        return $this->client;
    }

    protected function getToken(array $body): string
    {
        $this->client->setDefaultOptions([]);
        $response = $this->client->request('POST', self::AUTH_ENDPOINT, [
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

    protected function getReviewerEmail()
    {
        return self::CREDENTIALS[Role::REVIEWER->value]['email'];
    }

    protected function getContributorEmail()
    {
        return self::CREDENTIALS[Role::CONTRIBUTOR->value]['email'];
    }

    protected function getUserEmail()
    {
        return self::CREDENTIALS[Role::USER->value]['email'];
    }
}