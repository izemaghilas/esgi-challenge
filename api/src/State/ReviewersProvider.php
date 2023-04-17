<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Enums\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ReviewersProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$this->authorizationChecker->isGranted(Role::ADMIN->value)) {
            throw new AccessDeniedHttpException();
        }

        if ($operation instanceof CollectionOperationInterface) {
            $users = $this->entityManager->getRepository(User::class)->findAll();
            return array_filter($users, function ($user) {
                return in_array(Role::REVIEWER->value, $user->getRoles());
            });
        }

        throw new BadRequestHttpException();
    }
}