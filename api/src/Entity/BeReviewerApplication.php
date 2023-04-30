<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Enums\BeReviewerApplicationStatus;
use App\Repository\BeReviewerApplicationRepository;
use App\State\BeReviewerApplicationValidationProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BeReviewerApplicationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            itemUriTemplate: '/be_reviewer_applications/{id}'
        ),
        new Get(security: "is_granted('BE_REVIEWER_APPLICATION_VIEW', object)"),
        new Get(
            security: "is_granted('BE_REVIEWER_APPLICATION_VIEW', object)",
            uriTemplate: '/users/{id}/be-reviewer-application',
            uriVariables: [
                'id' => new Link(
                    fromClass: User::class,
                    toProperty: 'contributor'
                )
            ]
        ),
        new Post(
            security: "is_granted('ROLE_CONTRIBUTOR')",
            securityPostDenormalize: "is_granted('BE_REVIEWER_APPLICATION_CREATE', object)",
            itemUriTemplate: '/be_reviewer_applications/{id}'
        ),
        new Patch(
            security: "is_granted('BE_REVIEWER_APPLICATION_VALIDATE', object)",
            processor: BeReviewerApplicationValidationProcessor::class
        )
    ],
    normalizationContext: ['groups' => ['application:read']],
    denormalizationContext: ['groups' => ['application:create', 'application:admin:status']]
)]
class BeReviewerApplication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['application:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['application:read', 'application:create'])]
    private ?User $contributor = null;

    #[ORM\Column]
    #[Groups(['application:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['application:read', 'application:admin:status'])]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['application:read', 'application:create'])]
    private ?string $motivation = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['application:read', 'application:create'])]
    private ?string $skills = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = BeReviewerApplicationStatus::PENDING->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContributor(): ?User
    {
        return $this->contributor;
    }

    public function setContributor(?User $contributor): self
    {
        $this->contributor = $contributor;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(string $motivation): self
    {
        $this->motivation = $motivation;

        return $this;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(string $skills): self
    {
        $this->skills = $skills;

        return $this;
    }
}