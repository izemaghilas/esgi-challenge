<?php

namespace App\Entity;

use App\Repository\ReportedContentRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReportedContentRepository::class)]
#[ApiResource]
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
    #[ORM\JoinColumn(nullable: false)]
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
