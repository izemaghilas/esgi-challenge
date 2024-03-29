<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MailService;
use App\Service\VerifyEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ConfirmationEmailController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MailService $mailService,
        private readonly VerifyEmailService $verifyEmailService,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function __invoke(Request $request)
    {
        $body = $request->toArray();
        $errors = $this->validator->validate(null, new Assert\Email());
        if (
            null === $body ||
            !isset($body['email']) ||
            ($this->validator->validate($body['email'], new Assert\Email()))->count()
        ) {
            throw new BadRequestHttpException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $body['email']]);

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        $this->mailService->sendEmailConfirmationMail(
            $this->verifyEmailService->getSignedUrlForClient(strval($user->getId()), $user->getEmail()),
            $user->getEmail(),
            $user->getFirstname()
        );

        return new JsonResponse(['message' => 'confirmation mail sent'], 200);
    }
}