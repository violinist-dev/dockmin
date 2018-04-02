<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        // User account is expired, the user may be notified.
        if ($user->isExpired()) {
            throw new AccountExpiredException('Your account has expired. Contact site admin for more information.');
        }

        // User account is disabled.
        if (!$user->isActive()) {
            throw new \Exception('Your account is disabled.');
        }
    }
}