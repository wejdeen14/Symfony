<?php

namespace App\Controller;

use App\Entity\Amis;
use App\Entity\User;
use App\Form\AmisType;
use App\Form\UserType;
use App\Entity\Contenu;
use App\Form\ContenuType;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use App\Repository\AmisRepository;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;

#[IsGranted('ROLE_MEMBER')]
#[Route('/member/home')]
class MemberController extends AbstractController
{
    #[Route('/', name: 'app_member_home')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $search = new User(); // Assuming User is your entity class
    $form = $this->createForm(UserType::class, $search);
    $form->handleRequest($request);

    $searchResults = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $searchTerm = $form->get('name')->getData();
        $searchResults = $userRepository->search($searchTerm);
    }

    return $this->render('member/index.html.twig', [
        'form' => $form->createView(),
        'search' => $searchResults,
    ]);
    }

#[Route('/search/invite', name: 'app_member_search_invite')]
public function invite(Request $request,Security $security,UserRepository $userRepository,EntityManagerInterface $entityManager):Response{
    $amis=new Amis();
    $utilisateur=$security->getUser()->getUserIdentifier();
    $user=$userRepository->findOneBy(['name'=>$utilisateur]);
    $user2=$userRepository->find($request->request->get('userId'));
    $amis->setUtilisateur($user2);
    $amis->setAmis($user);
    $amis->setStatus('invite');
    $entityManager->persist($amis);
    $entityManager->flush();
    return $this->redirectToRoute('app_member_home');
}
#[Route('/request', name: 'app_member_request')]
public function request(Security $security,UserRepository $userRepository,Request $request,EntityManagerInterface $entityManager): Response
{
    $utilisateur=$security->getUser()->getUserIdentifier();
    $user=$userRepository->findOneBy(['name'=>$utilisateur]);
    $friends=$userRepository->friends($user,$entityManager);
    return $this->render('member/request.html.twig', [
        'friends'=>$friends,
    ]);
}
#[Route('/request/confirme', name: 'app_member_request_confirme')]
public function confirme(Security $security,EntityManagerInterface $entityManager, Request $request, AmisRepository $userRepository): Response
{
    $amis=new Amis();
    $amis2=$userRepository->find($request->request->get('userId'));
    dd($amis2);
    $amis2->setStatus('Amis');
    $entityManager->persist($amis);
    $entityManager->flush();
    return $this->redirectToRoute('app_member_home');
}
#[Route('/request/annuler/', name: 'app_member_request_annuler')]
public function delete(AmisRepository $repos,$id,EntityManagerInterface $entityManager){

        $friends = $repos->find($id);
        $entityManager->remove($friends);
        $entityManager->flush();
        return $this->redirectToRoute("app_member_request");
}
#[Route('/liste/friends', name: 'app_member_friends')]
public function ListeFriends(UserRepository $userRepository):Response{
    $friends=$userRepository->ListeFriends();
    return $this->render('member/request.html.twig', [
        'friends'=>$friends,
    ]);
}
#[Route('/add', name: 'app_member_add')]
public function addContenu(Security $security,Request $request,UserRepository $userRepository, Contenu $contenu, EntityManagerInterface $entityManager):Response{
    $contenu = new Contenu();
    $form = $this->createForm(ContenuType::class, $contenu);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $contenu->setText($form->get('text')->getData());
        $contenu->setDatePublication(new \DateTimeImmutable);
        $utilisateur=$security->getUser()->getUserIdentifier();
        $user=$userRepository->findOneBy(['name'=>$utilisateur]);
        $contenu->setUser($user);
        $entityManager->persist($contenu);
        $entityManager->flush();
    return $this->render('member/addContenu.html.twig', [
        'Form' => $form->createView(),
    ]);
}
}
#[Route('/liste/contenus', name: 'app_member_contenus')]
public function ListeContenus(UserRepository $userRepository,Security $security,Request $request,):Response{
    $utilisateur=$security->getUser()->getUserIdentifier();
    $user=$userRepository->findOneBy(['name'=>$utilisateur]);
    $contenus=$userRepository->ListeContenus($user);
    return $this->render('member/request.html.twig', [
        'contenus'=>$contenus,
    ]);
}

}

