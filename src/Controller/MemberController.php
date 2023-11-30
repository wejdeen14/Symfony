<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    #[Route('/search', name: 'app_member_search')]
public function search(UserRepository $userRepository, Request $request): Response
{
    $search = new User(); // Assuming User is your entity class
    $form = $this->createForm(UserType::class, $search);
    $form->handleRequest($request);

    $searchResults = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $searchTerm = $form->get('name')->getData();
        $searchResults = $userRepository->search($searchTerm);
    }

    return $this->render('member/search.html.twig', [
        'form' => $form->createView(),
        'search' => $searchResults,
    ]);
}

}
