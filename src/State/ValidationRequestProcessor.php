<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationRequestProcessor implements ProcessorInterface
{

    public function __construct(private ProcessorInterface $persistProcessor)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $reviewer = $data->getReviewerId();
        $course = $data->getContentId();
        if($course->getCreatorId() === $reviewer) {
            throw new BadRequestHttpException('validation request to course owner');
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}