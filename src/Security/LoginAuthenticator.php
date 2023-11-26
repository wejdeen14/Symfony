<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE_MEMBER = 'app_login_member';
    public const LOGIN_ROUTE_ADMIN = 'app_login_admin';
    public const LOGIN_ROUTE_MODERATEUR = 'app_login_moderateur';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $userRoles = $token->getRoleNames();
        if (in_array('ROLE_ADMIN', $userRoles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_home'));
        }
        // Check for other roles and redirect accordingly
        elseif (in_array('ROLE_MEMBER', $userRoles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_member_home'));
        }
        elseif (in_array('ROLE_MODERATEUR', $userRoles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_moderateur_home'));
        }

        // Add more conditions for other roles
    
        // If no specific role-based redirect, use a default redirect
        return new RedirectResponse($this->urlGenerator->generate('app_register_member'));
    }

    protected function getLoginUrl(Request $request): string{
        switch ($request->get('_route')) {
            case self::LOGIN_ROUTE_MEMBER:
                return $this->urlGenerator->generate(self::LOGIN_ROUTE_MEMBER);
            case self::LOGIN_ROUTE_ADMIN:
                return $this->urlGenerator->generate(self::LOGIN_ROUTE_ADMIN);
            case self::LOGIN_ROUTE_MODERATEUR:
                return $this->urlGenerator->generate(self::LOGIN_ROUTE_MODERATEUR);
            default:
                // Redirect to a default login route or throw an exception if needed
                return $this->urlGenerator->generate('app_register_member'); // Change 'app_login' to your default login route
        }
}
}
