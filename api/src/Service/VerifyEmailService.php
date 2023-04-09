<?php

namespace App\Service;

use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerifyEmailService
{
    protected const REGISTRATION_CONFIRMATION_ROUTE_NAME = 'registration_confirmation_route';
    protected const VERIFY_REGISTRATION_ENDPOINT = '/verify-registration';

    public function __construct(
        private readonly string $apiClientUrl,
        private readonly VerifyEmailHelperInterface $verifyEmailHelper
    ) {
    }

    /**
     * generate a signed url for registration confirmation
     * change base url to api client url
     * as when the user click the link should be redirected to api client (front end)
     * which sends the request back to the api
     */
    public function getSignedUrlForClient(string $userId, string $userEmail)
    {
        $signedUrl = $this->getSignedUrl($userId, $userEmail);
        return $this->apiClientUrl . self::VERIFY_REGISTRATION_ENDPOINT . '?url=' . urlencode($signedUrl);
    }

    public function getSignedUrl(string $userId, string $userEmail)
    {
        return ($this->verifyEmailHelper->generateSignature(
            self::REGISTRATION_CONFIRMATION_ROUTE_NAME,
            $userId,
            $userEmail,
            ['id' => $userId]
        )
        )->getSignedUrl();
    }

    public function verify(string $signedUrl, string $userId, string $userEmail)
    {
        $this->verifyEmailHelper->validateEmailConfirmation($signedUrl, $userId, $userEmail);
    }
}