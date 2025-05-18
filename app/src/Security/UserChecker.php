<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        /** @var \App\Entity\User $user */
        if (method_exists($user, 'isSuspended') && $user->isSuspended()) {
            throw new CustomUserMessageAuthenticationException('Ce compte est suspendu.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}