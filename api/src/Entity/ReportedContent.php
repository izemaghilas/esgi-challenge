<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\ReportedContentRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Post;

#[ORM\Entity(repositoryClass: ReportedContentRepository::class)]
#[
    ApiResource(
        operations: [
            new GetCollection(security: "is_granted('ROLE_ADMIN')"),
            new Post(),
        ]
    ),
    ApiFilter(SearchFilter::class, properties: [
        'contentId' => 'exact',
        'reporterId' => 'exact',
    ])
]
class ReportedContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reportedContents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Content $contentId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'reportedContents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotBlank]
    private ?User $reporterId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentId(): ?Content
    {
        return $this->contentId;
    }

    public function setContentId(?Content $contentId): self
    {
        $this->contentId = $contentId;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReporterId(): ?User
    {
        return $this->reporterId;
    }

    public function setReporterId(?User $reporterId): self
    {
        $this->reporterId = $reporterId;

        return $this;
    }
}
