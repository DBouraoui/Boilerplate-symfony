<?php

namespace App\DTO;

use App\Interface\DtoInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Data Transfer Object for password update request.
 *
 * This DTO is used when a user resets their password via a token received by email.
 */
class UpdatePasswordDto implements DtoInterface
{
    /**
     * The new password provided by the user.
     *
     * @var string
     */
    #[NotBlank(message: 'The password must not be blank.')]
    #[NotNull(message: 'The password field is required.')]
    #[Length(
        min: 8,
        max: 255,
        minMessage: "The password must be at least {{ limit }} characters long.",
        maxMessage: "The password cannot exceed {{ limit }} characters."
    )]
    public string $password;

    /**
     * The token associated with the password reset request.
     *
     * @var string
     */
    #[NotBlank(message: 'The token must not be blank.')]
    #[NotNull(message: 'The token field is required.')]
    public string $token;
}
