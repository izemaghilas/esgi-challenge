<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Content;
use App\Entity\ValidationRequest;
use App\Enums\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ContentReviewProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly ProcessorInterface $persistProcessor,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        if ($this->authorizationChecker->isGranted(Role::REVIEWER->value)) {
            $this->closeValidationRequest($result);
        }

        return $result;
    }

    private function closeValidationRequest(mixed $course)
    {
        if ($course instanceof Content) {
            $validationRequest = $this->entityManager->getRepository(ValidationRequest::class)->findOneBy(['contentId' => $course->getId()]);
            if (null !== $validationRequest) {
                $validationRequest->setActive(false);
                $this->entityManager->flush();
            }
        }
    }

}