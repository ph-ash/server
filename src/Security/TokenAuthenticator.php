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
    private const AUTH_TYPE_BEARER = 'Bearer ';

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            'message' => "Authorization header with 'Bearer TOKEN' Required"];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Bearer']);
    }

    public function supports(Request $request): bool
    {
        $supports = false;
        $wholeToken = $request->headers->get('Authorization');
        if ($wholeToken && substr_count($wholeToken, self::AUTH_TYPE_BEARER) > 0) {
            $token = explode(self::AUTH_TYPE_BEARER, $wholeToken);
            $supports = !empty($token[1]);
        }
        return $supports;
    }

    public function getCredentials(Request $request)
    {
        $wholeToken = $request->headers->get('Authorization');
        $token = explode(self::AUTH_TYPE_BEARER, $wholeToken);
        return $token[1];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $userProvider->loadUserByUsername($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['wrong credentials'], Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Bearer']);
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
