<?php

namespace App\DTO;

use App\Interface\DtoInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class UserForgetPasswordDto implements DtoInterface
{
    #[Email]
    #[NotNull]
    #[NotBlank]
    public string $email;
}
