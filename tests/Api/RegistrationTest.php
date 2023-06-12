<?php

namespace App\Tests\Api;

use App\Entity\User;
use App\Enums\Role;
use App\Service\VerifyEmailService;
use Faker\Factory;
use Faker\Generator as FakerGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class RegistrationTest extends AbstractTest
{

    const REGISTRATION_ENDPOINT = 'api/register';
    const SEND_CONFIRMATION_EMAIL_ENDPOINT = 'api/send-confirmation-email';
    const RESET_PASSWORD_ENDPOINT = 'api/reset-password';
    const SEND_RESET_PASSWORD_MAIL_ENDPOINT = 'api/send-reset-password-mail';

    private $user;
    private VerifyEmailService $verifyEmailHelper;
    private static FakerGenerator $faker;

    public static function setUpBeforeClass(): void
    {
        self::$faker = Factory::create();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->verifyEmailHelper = static::getContainer()->get(VerifyEmailService::class);

        $password = 'password';
        $this->user = [
            'firstname' => static::$faker->firstName(),
            'lastname' => static::$faker->lastName(),
            'email' => static::$faker->email(),
            'plainPassword' => $password,
        ];
    }

    private function registerUser(bool $isContributor = false): array
    {
        $client = $this->createClientForRole();

        if (true === $isContributor) {
            $this->user['contributor'] = true;
        }

        $response = $client->request('POST', self::REGISTRATION_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ],
            'json' => $this->user
        ]);

        $registeredUser = $response->toArray();
        $iri = $this->findIriBy(User::class, ['id' => $registeredUser['id']]);

        return [
            'id' => $registeredUser['id'],
            'iri' => $iri,
            ...$registeredUser,
        ];
    }

    public function testUserRegistration()
    {
        $user = $this->registerUser();
        $userInDB = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['id' => $user['id']]);


        $this->assertResponseIsSuccessful();

        $this->assertNotNull($userInDB);
        $this->assertFalse($userInDB->isActive());

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', $user['email']);
    }

    public function testUserRegisterAsContributor()
    {
        $user = $this->registerUser(isContributor: true);
        $userInDB = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['id' => $user['id']]);

        $this->assertResponseIsSuccessful();

        $this->assertNotNull($userInDB);
        $this->assertFalse($userInDB->isActive());
        $this->assertContains(Role::CONTRIBUTOR->value, $userInDB->getRoles());

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', $user['email']);

    }

    public function testRegistrationConfirmation()
    {
        $user = $this->registerUser();
        $signedUrl = $this->verifyEmailHelper->getSignedUrl(
            $user['id'],
            $user['email']
        );
        $this->createClientForRole()->request('GET', $signedUrl);
        $userInDB = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['id' => $user['id']]);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($userInDB->isActive());
    }

    public function testFailToConfirmRegistrationOnNoUserId()
    {
        $user = $this->registerUser();
        $signedUrl = $this->verifyEmailHelper->getSignedUrl(
            '',
            $user['email']
        );
        $this->createClientForRole()->request('GET', $signedUrl);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testFailToConfirmRegistrationOnNoUserEmail()
    {
        $user = $this->registerUser();
        $signedUrl = $this->verifyEmailHelper->getSignedUrl(
            $user['id'],
            ''
        );
        $this->createClientForRole()->request('GET', $signedUrl);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testFailToConfirmRegistrationOnUserNotExist()
    {
        $user = $this->registerUser();
        $uuid = Uuid::v7();
        $signedUrl = $this->verifyEmailHelper->getSignedUrl(
            $uuid->toRfc4122(),
            $user['email']
        );
        $this->createClientForRole()->request('GET', $signedUrl);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testFailToConfirmRegistrationOnModifiedSignedUrl()
    {
        $user = $this->registerUser();
        $signedUrl = $this->verifyEmailHelper->getSignedUrl(
            $user['id'],
            $user['email']
        );
        $signedUrl .= '&test=1';
        $this->createClientForRole()->request('GET', $signedUrl);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testSendConfirmationEmail()
    {
        $user = $this->registerUser();
        $this->createClientForRole()->request('POST', self::SEND_CONFIRMATION_EMAIL_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'email' => $user['email']
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', $user['email']);
    }

    public function testSendResetPasswordMail()
    {
        $userEmail = $this->getUserEmail();
        $this->createClientForRole()->request('POST', self::SEND_RESET_PASSWORD_MAIL_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'email' => $userEmail
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', $userEmail);
    }

    public function testResetPassword()
    {
        $newPassword = static::$faker->password();
        $userBeforePasswordReset = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $this->getUserEmail()]);
        $signedUrl = $this->verifyEmailHelper->getSignedUrlPasswordReset($userBeforePasswordReset->getId(), $userBeforePasswordReset->getEmail());

        $this->createClientForRole()->request('PATCH', $signedUrl, [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => [
                'plainPassword' => $newPassword,
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $userAfterPasswordReset = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $this->getUserEmail()]);
        $this->assertTrue(static::getContainer()->get(UserPasswordHasherInterface::class)->isPasswordValid($userAfterPasswordReset, $newPassword));
    }
}