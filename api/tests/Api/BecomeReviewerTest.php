<?php

namespace App\Tests\Api;

use App\Entity\BeReviewerApplication;
use App\Entity\User;
use App\Enums\BeReviewerApplicationStatus;
use App\Enums\Role;
use Symfony\Component\HttpClient\Exception\ClientException;

class BecomeReviewerTest extends AbstractTest
{

    private const BE_REVIEWER_APPLICATION_ENDPOINT = '/api/be_reviewer_applications';

    private $beReviewerApplication;

    public function setUp(): void
    {
        parent::setUp();

        $this->beReviewerApplication = [
            'motivation' => 'become reviewer',
            'skills' => 'Interpersonal Skills\n Consulting Experience'
        ];
    }


    private function sendApplication(?string $role = Role::CONTRIBUTOR->value): array
    {
        $contributorClient = self::createClientForRole($role);
        $response = $contributorClient->request('POST', self::BE_REVIEWER_APPLICATION_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ],
            'json' => $this->beReviewerApplication
        ]);
        $id = $response->toArray()['id'];
        $iri = $this->findIriBy(BeReviewerApplication::class, ['id' => $id]);

        return [
            'id' => $id,
            'iri' => $iri
        ];
    }

    private function validateApplication(string $status, string $iri, ?string $role = Role::ADMIN->value)
    {
        $client = self::createClientForRole($role);
        $client->request('PATCH', $iri, [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => [
                'status' => $status
            ]
        ]);
    }

    private function editApplication($iri, array $json, ?string $role = Role::ADMIN->value)
    {
        $client = self::createClientForRole($role);
        $client->request('PATCH', $iri, [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => $json
        ]);
    }

    public function testSendBeReviewerApplication()
    {
        $sentApplication = $this->sendApplication();

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(BeReviewerApplication::class);
        $this->assertNotNull(
            static::getContainer()
                ->get('doctrine')
                ->getRepository(BeReviewerApplication::class)
                ->findOneBy(['id' => $sentApplication['id']])
        );
    }

    public function testAdminAcceptsSentBeReviewerApplication()
    {
        $sentApplication = $this->sendApplication();
        $this->validateApplication(BeReviewerApplicationStatus::ACCEPTED->value, $sentApplication['iri']);
        $roles = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $this->getContributorEmail()])
            ->getRoles();

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(BeReviewerApplication::class);
        $this->assertEquals(
            BeReviewerApplicationStatus::ACCEPTED->value, static::getContainer()
                ->get('doctrine')
                ->getRepository(BeReviewerApplication::class)
                ->findOneBy(['id' => $sentApplication['id']])
                ->getStatus()
        );
        $this->assertContains(
            Role::CONTRIBUTOR->value,
            $roles
        );
        $this->assertContains(
            Role::REVIEWER->value,
            $roles
        );
    }

    public function testAdminRefusesSentBeReviewerApplication()
    {
        $sentApplication = $this->sendApplication();
        $this->validateApplication(BeReviewerApplicationStatus::REFUSED->value, $sentApplication['iri']);

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(BeReviewerApplication::class);
        $this->assertEquals(
            BeReviewerApplicationStatus::REFUSED->value, static::getContainer()
                ->get('doctrine')
                ->getRepository(BeReviewerApplication::class)
                ->findOneBy(['id' => $sentApplication['id']])
                ->getStatus()
        );
    }

    public function testReviewerCanNotRefuseOrAcceptBeReviewerApplication()
    {
        $sentApplication = $this->sendApplication();
        $this->validateApplication(BeReviewerApplicationStatus::ACCEPTED->value, $sentApplication['iri'], Role::REVIEWER->value);

        $this->assertResponseStatusCodeSame(403);
        $this->assertEquals(
            BeReviewerApplicationStatus::PENDING->value, static::getContainer()
                ->get('doctrine')
                ->getRepository(BeReviewerApplication::class)
                ->findOneBy(['id' => $sentApplication['id']])
                ->getStatus()
        );
    }

    public function testAdminCanNotSendBeReviewerApplication()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(403);

        $this->sendApplication(Role::ADMIN->value);
    }

    public function testReviewerCanNotSendBeReviewerApplication()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(403);

        $this->sendApplication(Role::REVIEWER->value);
    }

    public function testAdminCanNotEditBeReviewerApplication()
    {
        $sentApplication = $this->sendApplication();
        $persistedApplication = static::getContainer()
            ->get('doctrine')
            ->getRepository(BeReviewerApplication::class)
            ->findOneBy(['id' => $sentApplication['id']]);

        $this->editApplication($sentApplication['iri'], ['skills' => 'skills', 'motivation' => 'motivation']);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($this->beReviewerApplication['skills'], $persistedApplication->getSkills());
        $this->assertEquals($this->beReviewerApplication['motivation'], $persistedApplication->getMotivation());
    }

    public function testReviewerCanNotViewBeReviewerApplication()
    {
        $sentApplication = $this->sendApplication();
        $reviewerClient = self::createClientForRole(Role::REVIEWER->value);

        $reviewerClient->request('GET', $sentApplication['iri']);

        $this->assertResponseStatusCodeSame(403);
    }
}