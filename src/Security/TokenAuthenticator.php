<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = array(
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'authorization']);
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has('authorization');
    }

    public function getCredentials(Request $request)
    {
        return ['token' => $request->headers->get('authorization')];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        //TODO implement own UserProvider
        return $userProvider->loadUserByUsername($credentials['token']);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        //TODO later check if token is still valid and user is active
        //we have no passwords/credentials for now, username is token
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['wrong credentials'], Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'authorization']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
