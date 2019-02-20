<?php

namespace App\Security;

use App\Security\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser)
        {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user->getActive()) {
            throw new CustomUserMessageAuthenticationException(
                'Uw account is nog niet geactiveerd'
            );
        }
    }
}


