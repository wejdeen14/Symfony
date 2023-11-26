<?php

namespace App\Controller;

use App\Entity\Contenu;
use App\Form\ContenuType;
use App\Repository\UserRepository;
use App\Repository\ContenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_MODERATEUR')]
#[Route('/moderateur/home')]
class ModerateurController extends AbstractController
{
    #[Route('/', name: 'app_moderateur_home',methods:['GET'])]
    public function list(ContenuRepository $repos): Response
    {
        $contenu = $repos->findAll();

        return $this->render('moderateur/list.html.twig', [
            'contenu' => $contenu,
        ]);
    }
    #[Route('/{id}', name: 'app_moderateur_show',methods:['GET'])]
    public function show(Contenu $contenu): Response
    {

        return $this->render('moderateur/show.html.twig', [
            'contenu' => $contenu,
        ]);
    }
    #[Route('/{id}', name: 'app_moderateur_delete',methods:['POST'])]
    public function delete(Request $request, Contenu $contenu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contenu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contenu);
            $entityManager->flush();
        }

        return $this->render('moderateur/delete.html.twig', [
            'contenu' => $contenu,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_moderateur_edit',methods:['GET','POST'])]
    public function edit(Request $request, Contenu $contenu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContenuType::class, $contenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_moderateur_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('moderateur/edit.html.twig', [
            'contenu' => $contenu,
            'form' => $form,
        ]);
    }
}
