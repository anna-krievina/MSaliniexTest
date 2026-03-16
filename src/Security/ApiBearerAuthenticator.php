<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
class ApiBearerAuthenticator extends AbstractAuthenticator
{
    private const string AUTH_HEADER = 'Authorization';
    private const string BEARER_PREFIX = 'Bearer ';

    private string $apiToken;

    public function __construct(string $apiToken)
    {
        // configured in services.yaml
        $this->apiToken = $apiToken;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        // returning true otherwise it won't check for existence of header and let it be unauthorized
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get(self::AUTH_HEADER);

        if (!$authHeader || !str_starts_with($authHeader, self::BEARER_PREFIX)) {
            //  Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        $token = substr($authHeader, strlen(self::BEARER_PREFIX));

        if ($token !== $this->apiToken) {
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('Invalid API token');
        }

        // it's written like this (via ChatGPT), otherwise Symfony did user validation and returned "invalid credentials"
        // but user validation is not needed in this case as only the token needs to be validated
        return new SelfValidatingPassport(
            new UserBadge('api-user', function (string $userIdentifier) {
                return new InMemoryUser($userIdentifier, null, ['ROLE_API']);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    // public function start(Request $request, ?AuthenticationException $authException = null): Response
    // {
    //     /*
    //      * If you would like this class to control what happens when an anonymous user accesses a
    //      * protected page (e.g. redirect to /login), uncomment this method and make this class
    //      * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //      *
    //      * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //      */
    // }
}
