<?php

namespace App\Security\Hasher;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class CustomPasswordHasher implements PasswordHasherInterface
{
    use CheckPasswordLengthTrait;

    private $salt = 'Ninjastic Team 2024';

    public function hash(string $plainPassword): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException();
        }

        return hash("sha256", $this->salt . $plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword)) {
            return false;
        }

        $hashFromPlainPassword = hash("sha256", $this->salt . $plainPassword);

        return $hashedPassword == $hashFromPlainPassword;
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }
}
