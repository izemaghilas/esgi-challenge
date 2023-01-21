<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Repository\ContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(securityPostDenormalize: "is_granted('CONTENT_CREATE')"),
        new Get(),
        new Put(security: "is_granted('CONTENT_EDIT', object.creatorId)"),
        new Delete(security: "is_granted('CONTENT_DELETE', object.creatorId)"),
    ]
)]
class Content
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $mediaLink = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'contents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?User $creatorId = null;

    #[ORM\ManyToOne(inversedBy: 'contents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Category $categoryId = null;

    #[ORM\OneToMany(mappedBy: 'contentId', targetEntity: ReportedContent::class)]
    private Collection $reportedContents;

    public function __construct()
    {
        $this->reportedContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getMediaLink(): ?string
    {
        return $this->mediaLink;
    }

    public function setMediaLink(string $mediaLink): self
    {
        $this->mediaLink = $mediaLink;

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

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
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

    public function getCreatorId(): ?User
    {
        return $this->creatorId;
    }

    public function setCreatorId(?User $creatorId): self
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    public function getCategoryId(): ?Category
    {
        return $this->categoryId;
    }

    public function setCategoryId(?Category $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @return Collection<int, ReportedContent>
     */
    public function getReportedContents(): Collection
    {
        return $this->reportedContents;
    }

    public function addReportedContent(ReportedContent $reportedContent): self
    {
        if (!$this->reportedContents->contains($reportedContent)) {
            $this->reportedContents->add($reportedContent);
            $reportedContent->setContentId($this);
        }

        return $this;
    }

    public function removeReportedContent(ReportedContent $reportedContent): self
    {
        if ($this->reportedContents->removeElement($reportedContent)) {
            // set the owning side to null (unless already changed)
            if ($reportedContent->getContentId() === $this) {
                $reportedContent->setContentId(null);
            }
        }

        return $this;
    }
}
