<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;


class TokenAuthenticator extends JWTTokenAuthenticator
{

    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
        /** @var User $user */
        $user = parent::getUser(
            $preAuthToken,
            $userProvider
        );

        //var_dump($preAuthToken->getPayload()); die;
        if ($user->getPasswordChangeDate() && $preAuthToken->getPayload()['iat'] < $user->getPasswordChangeDate()) {
            throw new ExpiredTokenException();
        }

        return $user;
    }
}