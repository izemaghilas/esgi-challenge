<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\ValidationRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        operations: [
            new GetCollection(security: "is_granted('ROLE_ADMIN')"),
            new Post(securityPostDenormalize: "is_granted('VALIDATION_REQUEST_CREATE', object)"),
        ],
        normalizationContext: ['groups' => ['validation-request:read']],
        denormalizationContext: ['groups' => ['validation-request:create']]
    ),
    ApiFilter(SearchFilter::class, properties: [
        'reviewerId' => 'exact',
        'contentId' => 'exact',
    ])
]
#[ORM\Entity(repositoryClass: ValidationRequestRepository::class)]
class ValidationRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['validation-request:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['validation-request:read'])]
    private ?bool $active = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotBlank]
    #[Groups(['validation-request:read', 'validation-request:create'])]
    private ?User $reviewerId = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotBlank]
    #[Groups(['validation-request:read', 'validation-request:create'])]
    private ?Content $contentId = null;

    public function __construct()
    {
        $this->active = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getReviewerId(): ?User
    {
        return $this->reviewerId;
    }

    public function setReviewerId(User $reviewerId): self
    {
        $this->reviewerId = $reviewerId;

        return $this;
    }

    public function getContentId(): ?Content
    {
        return $this->contentId;
    }

    public function setContentId(Content $contentId): self
    {
        $this->contentId = $contentId;

        return $this;
    }
}
