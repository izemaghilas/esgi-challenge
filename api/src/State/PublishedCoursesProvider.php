<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Content;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class PublishedCoursesProvider implements ProviderInterface
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        
        if ($operation instanceof CollectionOperationInterface) {
            $courses = $this->entityManager->getRepository(Content::class)->findAll();
            return array_filter($courses, function ($course) {
                return $course->isActive();
            });
        }

        throw new BadRequestHttpException();
    }
}