<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\ValidationRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(securityPostDenormalize: "is_granted('VALIDATION_REQUEST_CREATE')"),
    ]
)]
#[ORM\Entity(repositoryClass: ValidationRequestRepository::class)]
class ValidationRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?User $reviewerId = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Content $contentId = null;

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
