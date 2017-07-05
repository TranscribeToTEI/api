<?php
namespace UserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\HttpUtils;

class AuthTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * Durée de validité du token en secondes, 10 jours
     */
    const TOKEN_VALIDITY_DURATION = 10 * 24 * 3600;

    protected $httpUtils;

    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    public function createToken(Request $request, $providerKey)
    {

        // Liste des exceptions
        if ($request->getMethod() === "POST" && (
                $this->httpUtils->checkRequestPath($request, '/auth-tokens') or
                $this->httpUtils->checkRequestPath($request, 'post_users'))) {
            return;
        } elseif ($request->getMethod() === "GET" && (
                $this->httpUtils->checkRequestPath($request, 'get_entities') or
                $this->httpUtils->checkRequestPath($request, 'get_entity') or
                $this->httpUtils->checkRequestPath($request, 'get_wills') or
                $this->httpUtils->checkRequestPath($request, 'get_will') or
                $this->httpUtils->checkRequestPath($request, 'get_resources') or
                $this->httpUtils->checkRequestPath($request, 'get_resource') or
                $this->httpUtils->checkRequestPath($request, 'get_testators') or
                $this->httpUtils->checkRequestPath($request, 'get_testator') or
                $this->httpUtils->checkRequestPath($request, 'get_transcripts') or
                $this->httpUtils->checkRequestPath($request, 'get_transcript') or
                $this->httpUtils->checkRequestPath($request, 'get_contents') or
                $this->httpUtils->checkRequestPath($request, 'get_content'))) {
            return;
        }

        $authTokenHeader = $request->headers->get('X-Auth-Token');

        if (!$authTokenHeader) {
            throw new BadCredentialsException('X-Auth-Token header is required');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $authTokenHeader,
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof AuthTokenUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of AuthTokenUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $authTokenHeader = $token->getCredentials();
        $authToken = $userProvider->getAuthToken($authTokenHeader);

        if (!$authToken || !$this->isTokenValid($authToken)) {
            throw new BadCredentialsException('Invalid authentication token');
        }

        $user = $authToken->getUser();
        $pre = new PreAuthenticatedToken(
            $user,
            $authTokenHeader,
            $providerKey,
            $user->getRoles()
        );

        // Nos utilisateurs n'ont pas de role particulier, on doit donc forcer l'authentification du token
        $pre->setAuthenticated(true);

        return $pre;
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * Vérifie la validité du token
     */
    private function isTokenValid($authToken)
    {
        return (time() - $authToken->getCreatedAt()->getTimestamp()) < self::TOKEN_VALIDITY_DURATION;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // Si les données d'identification ne sont pas correctes, une exception est levée
        throw $exception;
    }
}