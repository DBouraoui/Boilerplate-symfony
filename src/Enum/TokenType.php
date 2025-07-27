<?php

namespace App\Enum;

/**
 * Enum TokenType
 *
 * Represents all possible token types for user-related actions.
 *
 * This enum centralizes token usage across the application.
 * Add new token types here as needed (e.g., for 2FA, email change, account deletion, etc.).
 */
enum TokenType: string
{
    /** Token used for the password reset process */
    case FORGET_PASSWORD = 'forget_password';

    /** Token used for the email verification after registration */
    case REGISTER = 'register';
}
