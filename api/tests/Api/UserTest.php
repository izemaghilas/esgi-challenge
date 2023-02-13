<?php

namespace App\Tests\Api;

use App\Entity\User;
use App\Tests\Enums\Roles;

class UserTest extends AbstractTest
{

    private const ENDPOINT = "api/users";

    public function testAdminCanGetAllUsers()
    {
        self::createClientForRole(Roles::ADMIN->value)->request('GET', self::ENDPOINT);

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }
    
    public function testAdminCanCreateUser()
    {
        self::createClientForRole(Roles::ADMIN->value)->request('POST', self::ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'firstname' => 'john',
                'lastname' => 'doe',
                'email' => 'john@dev.fr',
                'plainPassword' => 'user',
                'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testAdminCanGetUser()
    {
        $iri = $this->findIriBy(User::class, ['email' => 'user1@dev.fr']);
        self::createClientForRole(Roles::ADMIN->value)->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri
        ]);
    }
    
    public function testAdminCanEditUser()
    {
        $iri = $this->findIriBy(User::class, ['email' => 'user1@dev.fr']);
        self::createClientForRole(Roles::ADMIN->value)->request('PATCH', $iri, [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => [
                'firstname' => 'john',
                'lastname' => 'doe',
                ]
            ]);
            
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'firstname' => 'john',
            'lastname' => 'doe',
        ]);
    }

    public function testAdminCanDeleteUser()
    {
        $iri = $this->findIriBy(User::class, ['email' => 'user1@dev.fr']);
        self::createClientForRole(Roles::ADMIN->value)->request('DELETE', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertNull(
            static::getContainer()
                ->get('doctrine')
                ->getRepository(User::class)
                ->findOneBy(['email' => 'user1@dev.fr']
            )
        );
    }

    public function testCanNotGetAllUsersIfNotAdmin()
    {
        self::createClientForRole(Roles::REVIEWER->value)->request('GET', self::ENDPOINT);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
        
        self::createClientForRole(Roles::CONTRIBUTOR->value)->request('GET', self::ENDPOINT);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);

        self::createClientForRole(Roles::USER->value)->request('GET', self::ENDPOINT);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
    }

    public function testCanNotGetUserIfNotAdmin()
    {
        $iri = $this->findIriBy(User::class, ['email' => 'user2@dev.fr']);

        self::createClientForRole(Roles::REVIEWER->value)->request('GET', $iri);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
        
        self::createClientForRole(Roles::CONTRIBUTOR->value)->request('GET', $iri);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);

        self::createClientForRole(Roles::USER->value)->request('GET', $iri);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
    }

    public function testCanNotCreateUserIfNotAdmin()
    {
        self::createClientForRole(Roles::REVIEWER->value)->request('POST', self::ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'firstname' => 'john',
                'lastname' => 'doe',
                'email' => 'john@dev.fr',
                'plainPassword' => 'user',
                'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
        
        self::createClientForRole(Roles::CONTRIBUTOR->value)->request('POST', self::ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'firstname' => 'john',
                'lastname' => 'doe',
                'email' => 'john@dev.fr',
                'plainPassword' => 'user',
                'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
        
        self::createClientForRole(Roles::USER->value)->request('POST', self::ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'firstname' => 'john',
                'lastname' => 'doe',
                'email' => 'john@dev.fr',
                'plainPassword' => 'user',
                'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
    }

    public function testCanNotEditUserIfNotAdmin()
    {
        $firstname = 'john1';
        $lastname = 'doe1';

        $iri = $this->findIriBy(User::class, ['email' => 'user2@dev.fr']);
        self::createClientForRole(Roles::REVIEWER->value)->request('PATCH', $iri, [
            'headers' => [
                'Content-Type' => '',
            ],
            'json' => [
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
        
        self::createClientForRole(Roles::CONTRIBUTOR->value)->request('PATCH', $iri, [
            'headers' => [
                'Content-Type' => '',
            ],
            'json' => [
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);

        self::createClientForRole(Roles::USER->value)->request('PATCH', $iri, [
            'headers' => [
                'Content-Type' => '',
            ],
            'json' => [
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
        
    }
    
    public function testCanNotDeleteUserIfNotAdmin()
    {
        $iri = $this->findIriBy(User::class, ['email' => 'user2@dev.fr']);
        self::createClientForRole(Roles::REVIEWER->value)->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
        
        self::createClientForRole(Roles::CONTRIBUTOR->value)->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);

        self::createClientForRole(Roles::USER->value)->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            '@type' => 'hydra:Error'
        ]);
    }
}