<?php

declare(strict_types=1);

/*
 * This file is part of Bilemo
 *
 * (c)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security\Voter;

use App\Entity\Consumer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ConsumerVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return \in_array($attribute, [self::EDIT, self::VIEW, self::DELETE], true)
            && $subject instanceof Consumer;
    }

    protected function voteOnAttribute(string $attribute, mixed $consumer, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if $consumer is Consumer continu or return false
        if (!$consumer instanceof Consumer) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $consumer->getUser()->getUserIdentifier() === $user->getUserIdentifier();
                break;
            case self::VIEW:
                return $consumer->getUser()->getUserIdentifier() === $user->getUserIdentifier();
                break;
            case self::DELETE:
                return $consumer->getUser()->getUserIdentifier() === $user->getUserIdentifier();
                break;
        }

        return false;
    }
}
