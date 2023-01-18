<?php

namespace App\Entity;

use App\Repository\RegisterTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;


#[ORM\Entity(repositoryClass: RegisterTokenRepository::class)]
#[ApiResource]
class RegisterToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expireAt = null;

    #[ORM\ManyToOne(inversedBy: 'registerTokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeImmutable
    {
        return $this->expireAt;
    }

    public function setExpireAt(\DateTimeImmutable $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
