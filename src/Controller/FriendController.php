<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Form\NewFriendFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends AbstractController {

    /**
     * @Route("/friends", name="friends")
     * @return RedirectResponse|Response
     */
    public function friends(){
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $friends = $this->getUser()->getFriends();
        $requests = $this->getUser()->getFriendRequests();

        $frienship = new Friendship();
        $frienship->setSender($this->getUser());
        $frienship->setPending(true);

        $form = $this->createForm(NewFriendFormType::class, $frienship);

        return $this->render('friends/friendList.html.twig', [
            'friends' => $friends,
            'requests' => $requests,
            'newFriendForm' => $form->createView()
        ]);
    }

}