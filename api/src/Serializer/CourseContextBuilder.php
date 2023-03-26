<?php
namespace App\Serializer;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Entity\Content;
use App\Enums\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class CourseContextBuilder implements SerializerContextBuilderInterface
{

    public function __construct(
        private readonly SerializerContextBuilderInterface $decorator,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorator->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;
        if (
            $resourceClass === Content::class &&
            isset($context['groups']) &&
            $this->authorizationChecker->isGranted(Role::ADMIN->value) &&
            false === $normalization // false -> denormalization | true -> normalization
        ) {
            $context['groups'][] = 'content:admin:review';
        }

        return $context;
    }
}