<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
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

    public const LOGIN_ROUTE= 'app_login';
    public const LOGIN_ROUTE_ADMIN = 'app_login_admin';

    public function __construct(private UrlGeneratorInterface $urlGenerator,private UserRepository $userRepository)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $user=$this->userRepository->findOneBy(['email'=>$email]);
        if($user != null){
        $userRoles = $user->getRoles();
        $url=$this->getLoginUrl($request);
        if (!in_array('ROLE_ADMIN', $userRoles) && str_contains($url,"admin")) {
            throw new AuthenticationException("only admin ");
        }
        if (in_array('ROLE_ADMIN', $userRoles) && str_contains($url,"test")) {
            throw new AuthenticationException("wrong utilisateur area ");
        }
    }
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        $pas= new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
        return $pas;

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
            case self::LOGIN_ROUTE:
                return $this->urlGenerator->generate(self::LOGIN_ROUTE);
            case self::LOGIN_ROUTE_ADMIN:
                return $this->urlGenerator->generate(self::LOGIN_ROUTE_ADMIN);
            default:
                // Redirect to a default login route or throw an exception if needed
                return $this->urlGenerator->generate('app_register_member'); // Change 'app_login' to your default login route
        }
}
}
