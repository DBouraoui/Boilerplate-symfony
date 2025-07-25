<?php

namespace App\DTO;

use App\Interface\DtoInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class UpdatePasswordDto implements DtoInterface
{
    #[NotBlank]
    #[NotNull]
    public string $password;

    #[NotNull]
    #[NotBlank]
    public string $token;
}
