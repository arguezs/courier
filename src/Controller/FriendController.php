<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\User;
use App\Form\NewFriendFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends AbstractController {

    /**
     * @Route("/friends", name="friends")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function friends(Request $request){
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $friends = $this->getUser()->getFriends();
        $requests = $this->getUser()->getFriendRequests();

        $friendship = new Friendship();
        $friendship->setSender($this->getUser());
        $friendship->setPending(true);

        $form = $this->createForm(NewFriendFormType::class, $friendship);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $userRepo = $this->getDoctrine()->getRepository(User::class);

            $user = $userRepo->findOneBy(['email' => $form->get('receiver')->getData()]);

            if ($user){
                $friendship->setReceiver($user);

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($friendship);
                $entityManager->flush();

                $this->addFlash('success', 'Friendship request sent');
            } else {
                $this->addFlash('failure', 'The user does not exist');
            }

        }

        return $this->render('friends/friendList.html.twig', [
            'friends' => $friends,
            'requests' => $requests,
            'newFriendForm' => $form->createView()
        ]);
    }

}