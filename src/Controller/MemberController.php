<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_MEMBER')]
#[Route('/member/home')]
class MemberController extends AbstractController
{
    #[Route('/', name: 'app_member_home')]
    public function index(): Response
    {
        return $this->render('member/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }
    #[Route('/request', name: 'app_member_request')]
    public function request(): Response
    {
        return $this->render('member/request.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }
}
