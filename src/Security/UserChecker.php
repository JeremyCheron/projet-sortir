<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if($user instanceof User)
        {
            if($user->isActive() === false)
            {
                throw new CustomUserMessageAccountStatusException($this->translator->trans('Your user account is not active.'));
            }
        }
    }


    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $user): void
    {
        // TODO: Implement checkPostAuth() method.
    }
}