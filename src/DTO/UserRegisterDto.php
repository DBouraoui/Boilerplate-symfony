<?php
namespace App\DTO;

use App\Interface\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterDto implements DtoInterface

{
    #[Assert\Email(message: "email is not valid")]
    #[Assert\NotBlank(message: "email is empty")]
    #[Assert\Length(min:1, max: 180,minMessage: "email is too short", maxMessage: "email is too long" )]
    public string $email;
    #[Assert\NotBlank(message: "password is empty")]
    #[Assert\Length(min: 8, max: 255, minMessage: "password is too short", maxMessage: "password is too long")]
    public string $password;
}
