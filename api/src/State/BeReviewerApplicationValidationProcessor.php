<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BeReviewerApplication;
use App\Enums\BeReviewerApplicationStatus;
use App\Enums\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class BeReviewerApplicationValidationProcessor implements ProcessorInterface
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
        if ($this->authorizationChecker->isGranted(Role::ADMIN->value)) {
            $this->makeContributorReviewer($result);
        }

        return $result;
    }

    private function makeContributorReviewer(mixed $validatedApplication)
    {
        if (
            $validatedApplication instanceof BeReviewerApplication &&
            $validatedApplication->getStatus() === BeReviewerApplicationStatus::ACCEPTED->value
        ) {
            $contibutor = $validatedApplication->getContributor();
            $roles = $contibutor->getRoles();
            array_push($roles, Role::REVIEWER->value);
            $contibutor->setRoles($roles);
            $this->entityManager->flush();
        }
    }
}