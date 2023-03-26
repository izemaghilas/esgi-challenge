<?php

namespace App\Tests\Api;

use App\Entity\Category;
use App\Entity\Content;
use App\Entity\User;
use App\Entity\ValidationRequest;
use App\Enums\Role;
use Symfony\Component\HttpClient\Exception\ClientException;

class PublishCourseTest extends AbstractTest
{
    const COURSES_ENDPOINT = 'api/contents';
    const CATEGORIES_ENDPOINT = 'api/categories';
    const VALIDATION_REQUESTS_ENDPOINT = '/api/validation_requests';
    const CATEGORY_TITLE = 'développement web';

    private $course;

    public function setUp(): void
    {
        parent::setUp();

        $this->course = [
            'title' => 'java',
            'description' => 'cours java pour débutant',
            'mediaLink' => '',
            'thumbnail' => '',
            'categoryId' => ''
        ];
    }

    private function publishCourse(?string $role = Role::CONTRIBUTOR->value)
    {
        $contributorClient = self::createClientForRole($role);
        $this->course['categoryId'] = $this->findIriBy(Category::class, ['title' => self::CATEGORY_TITLE]);
        $response = $contributorClient->request('POST', self::COURSES_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ],
            'json' => $this->course
        ]);

        $createdCourseId = $response->toArray()['id'];
        $iri = $this->findIriBy(Content::class, ['id' => $createdCourseId]);

        return [
            'id' => $createdCourseId,
            'iri' => $iri
        ];
    }

    private function sendValidationRequest()
    {
        $publishedCourse = $this->publishCourse();

        $adminClient = self::createClientForRole(Role::ADMIN->value);
        $reviewerIri = $this->findIriBy(User::class, ['email' => $this->getReviewerEmail()]);
        $courseIri = $publishedCourse['iri'];
        $response = $adminClient->request('POST', self::VALIDATION_REQUESTS_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ],
            'json' => ['reviewerId' => $reviewerIri, 'contentId' => $courseIri]
        ]);

        $id = $response->toArray()['id'];

        return [
            'id' => $id,
            'courseId' => $publishedCourse['id'],
            'courseIri' => $publishedCourse['iri']
        ];
    }

    public function testContributorPublishCourse()
    {
        $publishedCourse = $this->publishCourse();

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Content::class);
        $this->assertNotNull(
            static::getContainer()
                ->get('doctrine')
                ->getRepository(Content::class)
                ->findOneBy(['id' => $publishedCourse['id']])
        );
    }

    public function testAdminValidatePublishedCourse()
    {
        $publishedCourse = $this->publishCourse();

        $adminClient = self::createClientForRole(Role::ADMIN->value);
        $adminClient->request('PATCH', $publishedCourse['iri'], [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => [
                'active' => true
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Content::class);
        $this->assertTrue(
            static::getContainer()
                ->get('doctrine')
                ->getRepository(Content::class)
                ->findOneBy(
                    ['id' => $publishedCourse['id']]
                )->isActive()
        );
    }

    public function testAdminRequestValidationFromReviewerForPublishedCourse()
    {
        $validationRequest = $this->sendValidationRequest();

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(ValidationRequest::class);
        $this->assertTrue(
            static::getContainer()
                ->get('doctrine')
                ->getRepository(ValidationRequest::class)
                ->findOneBy(
                    ['id' => $validationRequest['id']]
                )->isActive()
        );
    }

    public function testReviewerValidatePublishedCourse()
    {
        $validationRequest = $this->sendValidationRequest();

        $reviewerClient = self::createClientForRole(Role::REVIEWER->value);
        $reviewerClient->request('PATCH', $validationRequest['courseIri'], [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => [
                'active' => true
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Content::class);
        $this->assertTrue(
            static::getContainer()
                ->get('doctrine')
                ->getRepository(Content::class)
                ->findOneBy(
                    ['id' => $validationRequest['courseId']]
                )->isActive()
        );
        $this->assertFalse(
            static::getContainer()
                ->get('doctrine')
                ->getRepository(ValidationRequest::class)
                ->findOneBy(
                    ['id' => $validationRequest['id']]
                )->isActive()
        );
    }

    public function testAdminCanNotPublishCourse()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(403);

        $this->publishCourse(Role::ADMIN->value);
    }

    public function testReviewerCanNotPublishCourse()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(403);

        $this->publishCourse(Role::REVIEWER->value);
    }
}