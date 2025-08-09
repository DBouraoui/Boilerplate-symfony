<?php

namespace App\DTO;

use App\Interface\DtoInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Data Transfer Object for requesting a password reset.
 *
 * This DTO is used when a user submits their email address to receive
 * a password reset link.
 */
class UserForgetPasswordDto implements DtoInterface
{
    /**
     * The email address of the user requesting the password reset.
     *
     * @var string
     */
    #[Email(message: 'The email address format is invalid.')]
    #[NotNull(message: 'The email field is required.')]
    #[NotBlank(message: 'The email must not be blank.')]
    #[Length(
        min: 1,
        max: 180,
        minMessage: "The email address is too short.",
        maxMessage: "The email address is too long."
    )]
    public string $email;
}
