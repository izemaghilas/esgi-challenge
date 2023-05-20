<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Controller\ThumbnailController;
use App\Controller\VideoController;
use App\Repository\ContentRepository;
use App\State\ContentReviewProcessor;
use App\State\PublishedCoursesProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
#[Vich\Uploadable]
#[
    ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(uriTemplate: '/contents/published',
            provider: PublishedCoursesProvider::class),
        new GetCollection(
            uriTemplate: '/users/{id}/contents',
            uriVariables: [
                'id' => new Link(
                    fromClass: User::class,
                    fromProperty: 'contents'
                )
            ]
        ),
        new Post(
            security: "is_granted('ROLE_CONTRIBUTOR')",
            securityPostDenormalize: "is_granted('CONTENT_CREATE', object)",
            inputFormats: ['multipart' => ['multipart/form-data']]
        ),
        new Get(),
        new Get(
            name: 'content_thumbnails',
            uriTemplate: 'thumbnails/{thumbnail}',
            controller: ThumbnailController::class,
            read: false
        ),
        new Get(
            name: 'content_videos',
            uriTemplate: '/contents/{id}/videos/{video}',
            controller: VideoController::class,
            read: false
        ),
        new Put(security: "is_granted('CONTENT_EDIT', object.creatorId)"),
        new Delete(security: "is_granted('CONTENT_DELETE', object.creatorId)"),
        new Patch(
            security: "is_granted('CONTENT_REVIEW', object)",
            processor: ContentReviewProcessor::class
        )
    ],
    normalizationContext: ['groups' => ['content:read']],
    denormalizationContext: ['groups' => ['content:create']]
),
    ApiFilter(SearchFilter::class, properties: [
        'categoryId' => 'exact',
        'creatorId' => 'exact',
    ]),
    ApiFilter(OrderFilter::class, properties: ['createdAt'], arguments: ['orderParameterName' => 'order'])
]
class Content
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['content:read', 'validation-request:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['content:read', 'content:create', 'validation-request:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['content:read', 'content:create', 'validation-request:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $mediaLink = null;

    #[Vich\UploadableField(mapping: 'medialinks', fileNameProperty: 'mediaLink')]
    // TODO: check video size
    #[Assert\File(mimeTypes: ['video/*'], mimeTypesMessage: 'Please upload a valid video file')]
    #[Groups(['content:create'])]
    private ?File $mediaLinkFile = null;

    #[Groups(['content:video:read'])]
    private ?string $mediaLinkUrl = null;

    #[ORM\Column]
    #[Groups(['content:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail = null;

    #[Vich\UploadableField(mapping: 'thumbnails', fileNameProperty: 'thumbnail')]
    #[Assert\File(mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Please upload a valid image file')]
    #[Groups(['content:create'])]
    private ?File $thumbnailFile = null;

    #[Groups(['content:read', 'validation-request:read'])]
    private $thumbnailUrl = null;

    #[ORM\Column]
    #[Groups(['content:read', 'content:review'])]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'contents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotBlank]
    #[Groups(['content:read', 'content:create', 'validation-request:read'])]
    private ?User $creatorId = null;

    #[ORM\ManyToOne(inversedBy: 'contents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['content:read', 'content:create', 'validation-request:read'])]
    private ?Category $categoryId = null;

    #[ORM\OneToMany(mappedBy: 'contentId', targetEntity: ReportedContent::class)]
    private Collection $reportedContents;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Groups(['content:read', 'content:create', 'validation-request:read'])]
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?float $price = null;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Purchase::class, orphanRemoval: true)]
    private Collection $purchases;

    public function __construct()
    {
        $this->active = false;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->reportedContents = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->purchases = new ArrayCollection();
    }

    public function getId(): ?Uuid
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

    public function getMediaLinkFile(): ?File
    {
        return $this->mediaLinkFile;
    }

    public function setMediaLinkFile(?File $file): void
    {
        $this->mediaLinkFile = $file;
        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getMediaLinkUrl(): ?string
    {
        return $this->mediaLinkUrl;
    }

    public function setMediaLinkUrl(?string $url): void
    {
        $this->mediaLinkUrl = $url;
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

    public function getThumbNailFile(): ?File
    {
        return $this->thumbnailFile;
    }

    public function setThumbnailFile(?File $file = null): void
    {
        $this->thumbnailFile = $file;
        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl($url): void
    {
        $this->thumbnailUrl = $url;
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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setCourse($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCourse() === $this) {
                $comment->setCourse(null);
            }
        }

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setCourse($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getCourse() === $this) {
                $purchase->setCourse(null);
            }
        }

        return $this;
    }
}