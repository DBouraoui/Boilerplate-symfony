<?php

namespace App\DTO;

use App\Interface\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for user registration.
 *
 * This DTO captures the email and password submitted during the sign-up process.
 */
class UserRegisterDto implements DtoInterface
{
    /**
     * The user's email address.
     *
     * Must be a valid email format, not empty, and between 1 and 180 characters.
     */
    #[Assert\Email(message: "The email address is not valid.")]
    #[Assert\NotBlank(message: "The email field must not be empty.")]
    #[Assert\Length(
        min: 1,
        max: 180,
        minMessage: "The email address is too short.",
        maxMessage: "The email address is too long."
    )]
    public string $email;

    /**
     * The user's password.
     *
     * Must not be empty and must be between 8 and 255 characters.
     */
    #[Assert\NotBlank(message: "The password field must not be empty.")]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "The password must be at least {{ limit }} characters long.",
        maxMessage: "The password cannot exceed {{ limit }} characters."
    )]
    public string $password;
}
