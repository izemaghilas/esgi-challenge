<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[
    ApiResource(
        operations: [
            new GetCollection(security: "is_granted('ROLE_ADMIN')"),
            new Post(),
            new Delete(security: "is_granted('COMMENT_DELETE')"),
        ]
    ), 
    ApiFilter(SearchFilter::class, properties: [
        'commenterId' => 'exact',
        'course' => 'exact',
    ])
]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?User $commenterId = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Content $course = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCommenterId(): ?User
    {
        return $this->commenterId;
    }

    public function setCommenterId(?User $commenterId): self
    {
        $this->commenterId = $commenterId;

        return $this;
    }

    public function getCourse(): ?Content
    {
        return $this->course;
    }

    public function setCourse(?Content $course): self
    {
        $this->course = $course;

        return $this;
    }
}
