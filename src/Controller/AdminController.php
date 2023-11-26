<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/home')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_home',methods: ['GET'])]
    public function list(UserRepository $repos): Response
    {
            $member = $repos->findByRole();

        return $this->render('admin/list.html.twig', [
            'member' => $member,
        ]);
    }
    #[Route('/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(User $member): Response
    {
        return $this->render('admin/show.html.twig', [
            'member' => $member,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_admin_edit', methods: ['GET','POST'])]
    public function edit(Request $request, User $member, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/edit.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, User $member, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$member->getId(), $request->request->get('_token'))) {
            $entityManager->remove($member);
            $entityManager->flush();
        }
        return $this->render('admin/delete.html.twig', [
            'member' => $member,
        ]);
    }


}
