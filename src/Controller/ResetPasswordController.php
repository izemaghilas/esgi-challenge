<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\VerifyEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[AsController]
class ResetPasswordController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ValidatorInterface $validator,
        private readonly VerifyEmailService $verifyEmailService
    ) {
    }

    public function __invoke(Request $request)
    {
        $userId = $request->get('id');
        $body = $request->toArray();

        if (
            !isset($body['plainPassword']) ||
            $this->validator->validate($body['plainPassword'], new Assert\NotCompromisedPassword())->count()
        ) {
            throw new UnprocessableEntityHttpException();
        }

        if (
            null === $userId ||
            false === Uuid::isValid($userId)
        ) {
            throw new NotFoundHttpException('');
        }

        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new NotFoundHttpException('');
        }

        try {
            $this->verifyEmailService->verify($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            throw new BadRequestHttpException();
        }

        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $body['plainPassword']);
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'successfully reset password'], 200);
    }
}