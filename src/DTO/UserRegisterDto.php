<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterDto

{
    #[Assert\Email(message: "email is not valid")]
    #[Assert\Length( max: 180, maxMessage: "email is too long" )]
    public string $email;
    #[Assert\Length(min: 8, max: 255, minMessage: "password is too short", maxMessage: "password is too long")]
    public string $password;

    #[Assert\NotNull]
    private \DateTimeImmutable $createdAt;
    #[Assert\NotNull]
    private \DateTimeImmutable $updatedAt;

    public function __construct() {
        $now =  new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }
}
