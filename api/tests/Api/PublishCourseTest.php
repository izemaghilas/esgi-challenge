<?php

namespace App\Tests\Api;

use App\Entity\Category;
use App\Entity\Content;
use App\Entity\User;
use App\Entity\ValidationRequest;
use App\Enums\Role;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PublishCourseTest extends AbstractTest
{
    const COURSES_ENDPOINT = 'api/contents';
    const CATEGORIES_ENDPOINT = 'api/categories';
    const VALIDATION_REQUESTS_ENDPOINT = '/api/validation_requests';
    const CATEGORY_TITLE = 'développement web';

    private $course;

    private static FileSystem $fileSystem;

    public static function setUpBeforeClass(): void
    {
        self::$fileSystem = new Filesystem();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->course = [
            'title' => 'java',
            'description' => 'cours java pour débutant',
            'thumbnail' => $this->getThumbnail(),
            'mediaLink' => $this->getVideo(),
            'categoryId' => ''
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->removeAllThumbnails();
        $this->removeAllVideos();
    }

    private function getThumbnail()
    {
        $path = dirname(__DIR__) . '/files/thumbnail.png';
        $tempPath = dirname(__DIR__) . '/files/thumbnail-copy.png';
        copy($path, $tempPath); // make a copy to not move the original file
        return new UploadedFile($tempPath, 'thumbnail.png', 'image/png', null, true);
    }

    private function getVideo()
    {
        $path = dirname(__DIR__) . '/files/video.mp4';
        $tempPath = dirname(__DIR__) . '/files/video-copy.mp4';
        copy($path, $tempPath); // make a copy to not move the original file
        return new UploadedFile($tempPath, 'video.mp4', 'video/mp4', null, true);
    }

    private function removeAllThumbnails()
    {
        $thumbnailsPath = dirname(__DIR__) . '/thumbnails';
        $tempThumbnail = dirname(__DIR__) . '/files/thumbnail-copy.png';

        if (true === static::$fileSystem->exists($thumbnailsPath)) {
            static::$fileSystem->remove($thumbnailsPath);
        }

        if (true === static::$fileSystem->exists($tempThumbnail)) {
            static::$fileSystem->remove($tempThumbnail);
        }
    }

    private function removeAllVideos()
    {
        $videosPath = dirname(__DIR__) . '/videos';
        $tempVideoPath = dirname(__DIR__) . '/files/video-copy.mp4';

        if (true === static::$fileSystem->exists($videosPath)) {
            static::$fileSystem->remove($videosPath);
        }

        if (true === static::$fileSystem->exists($tempVideoPath)) {
            static::$fileSystem->remove($tempVideoPath);
        }
    }

    private function publishCourse(?string $role = Role::CONTRIBUTOR->value, ?float $price = 0.0): array
    {
        $contributorClient = self::createClientForRole($role);
        $this->course['categoryId'] = $this->findIriBy(Category::class, ['title' => self::CATEGORY_TITLE]);
        $response = $contributorClient->request('POST', self::COURSES_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'multipart/form-data'
            ],
            'extra' => [
                'parameters' => [
                    'title' => $this->course['title'],
                    'description' => $this->course['description'],
                    'categoryId' => $this->course['categoryId'],
                    'price' => $price
                ],
                'files' => [
                    'thumbnailFile' => $this->course['thumbnail'],
                    'mediaLinkFile' => $this->course['mediaLink'],
                ]
            ],
        ]);

        $publishedCourse = $response->toArray();
        $createdCourseId = $publishedCourse['id'];
        $thumbnail = $publishedCourse['thumbnailUrl'];
        $mediaLink = $publishedCourse['mediaLinkUrl'];
        $iri = $this->findIriBy(Content::class, ['id' => $createdCourseId]);

        return [
            'id' => $createdCourseId,
            'iri' => $iri,
            'thumbnail' => $thumbnail,
            'mediaLink' => $mediaLink
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

    public function testContributorPublishFreeCourse()
    {
        $price = 0.0;
        $publishedCourse = $this->publishCourse(price: $price);
        $courseInDb = static::getContainer()
            ->get('doctrine')
            ->getRepository(Content::class)
            ->findOneBy(['id' => $publishedCourse['id']]);

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Content::class);
        $this->assertNotNull($publishedCourse['thumbnail']);
        $this->assertNotNull($publishedCourse['mediaLink']);
        $this->assertNotNull($courseInDb);
        $this->assertEquals($price, $courseInDb->getPrice());
    }

    public function testContributorPublishPaidCourse()
    {
        $price = 15.99;
        $publishedCourse = $this->publishCourse(price: $price);

        $courseInDb = static::getContainer()
            ->get('doctrine')
            ->getRepository(Content::class)
            ->findOneBy(['id' => $publishedCourse['id']]);

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Content::class);
        $this->assertNotNull($publishedCourse['thumbnail']);
        $this->assertNotNull($publishedCourse['mediaLink']);
        $this->assertNotNull($courseInDb);
        $this->assertEquals($price, $courseInDb->getPrice());
    }

    public function testContributorPublishCourseWithNegativePriceValue() 
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(422);

        $price = -15.99;
        $this->publishCourse(price: $price);
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

    public function testAdminCanViewCourseVideoIfNotPublished()
    {
        $publishedCourse = $this->publishCourse();
        $adminClient = $this->createClientForRole(Role::ADMIN->value);
        $response = $adminClient->request('GET', $publishedCourse['iri']);
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertNotNull($data['mediaLinkUrl']);
    }

    public function testReviewerCanViewCourseVideoIfNotPublished()
    {
        $publishedCourse = $this->publishCourse();
        $reviewerClient = $this->createClientForRole(Role::REVIEWER->value);
        $response = $reviewerClient->request('GET', $publishedCourse['iri']);
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertNotNull($data['mediaLinkUrl']);
    }

    public function testAdminCanNotViewCourseVideoIfPublished()
    {
        $publishedCourse = $this->publishCourse();
        $adminClient = $this->createClientForRole(Role::ADMIN->value);
        $adminClient->request('PATCH', $publishedCourse['iri'], [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => [
                'active' => true
            ]
        ]);
        $response = $adminClient->request('GET', $publishedCourse['iri']);
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayNotHasKey('mediaLinkUrl', $data);
    }

    public function testCourseVideoIsDownloaded()
    {
        $publishedCourse = $this->publishCourse();
        $adminClient = $this->createClientForRole(Role::ADMIN->value);
        $adminClient->request('GET', $publishedCourse['mediaLink']);

        $this->assertResponseIsSuccessful();
    }
}