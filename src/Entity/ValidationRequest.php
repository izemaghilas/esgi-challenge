<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Repository\ValidationRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        operations: [
            new GetCollection(security: "is_granted('ROLE_ADMIN')"),
            new GetCollection(
                security: "is_granted('ROLE_REVIEWER')", 
                uriTemplate: '/users/{id}/validation-requests',
                uriVariables: [
                    'id' => new Link(fromClass: User::class, toProperty: 'reviewerId')
                ],
            ),
            new Post(securityPostDenormalize: "is_granted('VALIDATION_REQUEST_CREATE', object)"),
        ],
        normalizationContext: ['groups' => ['validation-request:read']],
        denormalizationContext: ['groups' => ['validation-request:create']]
    ),
    ApiFilter(SearchFilter::class, properties: [
        'reviewerId' => 'exact',
        'contentId' => 'exact',
        'active' => 'exact',
    ])
]
#[ORM\Entity(repositoryClass: ValidationRequestRepository::class)]
class ValidationRequest
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['validation-request:read'])]
    private ?Uuid $id = null;

    #[ORM\Column]
    #[Groups(['validation-request:read', 'content:read'])]
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

    #[ORM\Column]
    #[Groups(['validation-request:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->active = true;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
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
        $this->updatedAt = new \DateTimeImmutable();

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
