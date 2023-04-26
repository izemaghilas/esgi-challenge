<?php

namespace App\Entity;

use App\Controller\VerifyRegistrationController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\ReviewersProvider;
use App\State\UserPasswordHasher;
use App\State\UserProcessor;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            provider: ReviewersProvider::class
        ),
        new Get(security: "is_granted('USER_VIEW', object)"),
        new Patch(
            security: "is_granted('USER_EDIT', object)",
            processor: UserPasswordHasher::class
        ),
        new Delete(security: "is_granted('USER_DELETE', object)"),
        new Post(
            '/register',
            processor: UserProcessor::class
        ),
        new Get(
            name: 'registration_confirmation_route',
            uriTemplate: '/verify-registration',
            controller: VerifyRegistrationController::class,
            read: false,
            normalizationContext: ['groups' => []],
        ),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:create', 'user:update']],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[Groups(['user:create', 'user:read', 'user:update'])]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:create', 'user:update'])]
    #[Assert\NotCompromisedPassword]
    private ?string $plainPassword = null;

    #[Groups(['user:create', 'user:read'])]
    #[Assert\Positive]
    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:create', 'user:read', 'comment:read', 'application:read', 'content:read', 'validation-request:read'])]

    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:create', 'user:read', 'comment:read', 'application:read', 'content:read', 'validation-request:read'])]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: ForgotPasswordToken::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $forgotPasswordTokens;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: RegisterToken::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $registerTokens;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user:read'])]
    private ?Subscription $subscriptionId = null;

    #[ORM\OneToMany(mappedBy: 'creatorId', targetEntity: Content::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $contents;

    #[ORM\OneToMany(mappedBy: 'reporterId', targetEntity: ReportedContent::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $reportedContents;

    #[ORM\OneToMany(mappedBy: 'commenterId', targetEntity: Comment::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $comments;

    #[ORM\Column]
    private ?bool $active = null;

    public function __construct()
    {
        $this->active = false;
        $this->createdAt = new \DateTimeImmutable();
        $this->forgotPasswordTokens = new ArrayCollection();
        $this->registerTokens = new ArrayCollection();
        $this->contents = new ArrayCollection();
        $this->reportedContents = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $painPassword): self
    {
        $this->plainPassword = $painPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    /**
     * @return Collection<int, ForgotPasswordToken>
     */
    public function getForgotPasswordTokens(): Collection
    {
        return $this->forgotPasswordTokens;
    }

    public function addForgotPasswordToken(ForgotPasswordToken $forgotPasswordToken): self
    {
        if (!$this->forgotPasswordTokens->contains($forgotPasswordToken)) {
            $this->forgotPasswordTokens->add($forgotPasswordToken);
            $forgotPasswordToken->setUserId($this);
        }

        return $this;
    }

    public function removeForgotPasswordToken(ForgotPasswordToken $forgotPasswordToken): self
    {
        if ($this->forgotPasswordTokens->removeElement($forgotPasswordToken)) {
            // set the owning side to null (unless already changed)
            if ($forgotPasswordToken->getUserId() === $this) {
                $forgotPasswordToken->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RegisterToken>
     */
    public function getRegisterTokens(): Collection
    {
        return $this->registerTokens;
    }

    public function addRegisterToken(RegisterToken $registerToken): self
    {
        if (!$this->registerTokens->contains($registerToken)) {
            $this->registerTokens->add($registerToken);
            $registerToken->setUserId($this);
        }

        return $this;
    }

    public function removeRegisterToken(RegisterToken $registerToken): self
    {
        if ($this->registerTokens->removeElement($registerToken)) {
            // set the owning side to null (unless already changed)
            if ($registerToken->getUserId() === $this) {
                $registerToken->setUserId(null);
            }
        }

        return $this;
    }

    public function getSubscriptionId(): ?Subscription
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(Subscription $subscriptionId): self
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    /**
     * @return Collection<int, Content>
     */
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(Content $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents->add($content);
            $content->setCreatorId($this);
        }

        return $this;
    }

    public function removeContent(Content $content): self
    {
        if ($this->contents->removeElement($content)) {
            // set the owning side to null (unless already changed)
            if ($content->getCreatorId() === $this) {
                $content->setCreatorId(null);
            }
        }

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
            $reportedContent->setReporterId($this);
        }

        return $this;
    }

    public function removeReportedContent(ReportedContent $reportedContent): self
    {
        if ($this->reportedContents->removeElement($reportedContent)) {
            // set the owning side to null (unless already changed)
            if ($reportedContent->getReporterId() === $this) {
                $reportedContent->setReporterId(null);
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
            $comment->setCommenterId($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCommenterId() === $this) {
                $comment->setCommenterId(null);
            }
        }

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
}