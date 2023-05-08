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
use Symfony\Component\Uid\Uuid;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[AsController]
class VerifyRegistrationController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly VerifyEmailService $verifyEmailService
    ) {
    }

    public function __invoke(Request $request)
    {
        $userId = $request->get('id');
        if (null === $userId || false === Uuid::isValid($userId)) {
            throw new BadRequestHttpException();
        }

        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new NotFoundHttpException('');
        }

        try {
            $this->verifyEmailService->verify($request->getUri(), $user->getId(), $user->getEmail());
            $user->setActive(true);
            $this->entityManager->flush();
        } catch (VerifyEmailExceptionInterface $e) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse(['message' => 'account verified']);
    }
}