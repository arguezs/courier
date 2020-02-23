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

    /**
     * @Route("/accept/{requestId}", name="accept_friendship")
     * @param $requestId
     * @return RedirectResponse
     */
    public function acceptFriendship($requestId) {
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $friendship = $this
                        ->getDoctrine()
                        ->getRepository(Friendship::class)
                        ->find($requestId);

        if (!$friendship || !$friendship->isPending() ||
            $friendship->getReceiver() != $this->getUser())
            return $this->redirectToRoute('error', ['errorCode' => 404]);

        $friendship->setPending(false);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($friendship);
        $entityManager->flush();

        $this->addFlash('success', 'You are now friends with ' . $friendship->getSender()->getName());

        return $this->redirectToRoute('friends');
    }

    /**
     * @Route("/decline/{requestId}", name="decline_friendship")
     * @param $requestId
     * @return RedirectResponse
     */
    public function declineFriendship($requestId) {
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $friendship = $this
            ->getDoctrine()
            ->getRepository(Friendship::class)
            ->find($requestId);

        if (!$friendship || !$friendship->isPending() ||
            $friendship->getReceiver() != $this->getUser())
            return $this->redirectToRoute('error', ['errorCode' => 404]);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($friendship);
        $entityManager->flush();

        $this->addFlash('success', 'Friendship declined');

        return $this->redirectToRoute('friends');
    }

    /**
     * @Route("/delete-friendship/{friendId}", name="delete_friendship")
     * @param $friendId
     * @return RedirectResponse
     */
    public function deleteFriendship($friendId) {
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $friend = $this
                    ->getDoctrine()
                    ->getRepository(User::class)
                    ->find($friendId);

        if (!$friend)
            return $this->redirectToRoute('error', 404);

        $friendshipRepo = $this->getDoctrine()->getRepository(Friendship::class);

        $friendship = $friendshipRepo->findOneBy([
            'sender' => $this->getUser(),
            'receiver' => $friend
        ]);

        if (!$friendship)
            $friendship = $friendshipRepo->findOneBy([
                'sender' => $friend,
                'receiver' => $this->getUser()
            ]);

        if (!$friendship || $friendship->isPending() ||
            !($friendship->getSender() == $this->getUser() || $friendship->getReceiver() == $this->getUser()))
            return $this->redirectToRoute('error', 404);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($friendship);
        $entityManager->flush();

        $this->addFlash('success',
            'You are no longer friends with ' . ($friendship->getSender() == $this->getUser() ? $friendship->getReceiver()->getName() : $friendship->getSender()->getName()));

        return $this->redirectToRoute('friends');
    }

}