<?php

namespace App\Entity;

use App\Enum\TokenType;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserTokenRepository::class)]
class UserToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $token = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiredAt = null;

    #[ORM\OneToOne(inversedBy: 'userToken')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?User $relatedUser = null;

    #[ORM\Column(length: 20)]
    private ?TokenType $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?Uuid
    {
        return $this->token;
    }

    public function setToken(Uuid $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeImmutable $expiredAt): static
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    public function getRelatedUser(): ?User
    {
        return $this->relatedUser;
    }

    public function setRelatedUser(?User $user): static
    {
        $this->relatedUser = $user;

        if ($user && $user->getUserToken() !== $this) {
            $user->setUserToken($this);
        }

        return $this;
    }

    public function getType(): TokenType
    {
        return $this->type;
    }

    public function setType(TokenType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
