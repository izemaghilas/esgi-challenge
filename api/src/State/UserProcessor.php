<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Service\MailService;
use App\Service\VerifyEmailService;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly UserPasswordHasher $userPasswordHasher,
        private readonly MailService $mailService,
        private readonly VerifyEmailService $verifyEmailService
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->userPasswordHasher->process($data, $operation, $uriVariables, $context);
        $this->sendConfirmationMail($user);
    }

    private function sendConfirmationMail(User $user): void
    {
        $this->mailService->sendEmailConfirmationMail(
            $this->verifyEmailService->getSignedUrlForClient(strval($user->getId()), $user->getEmail()),
            $user->getEmail(),
            $user->getFirstname()
        );
    }
}