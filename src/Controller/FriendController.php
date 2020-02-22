<?php

namespace App\Controller;

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

        return $this->render('friends/friendList.html.twig', [
            'friends' => $friends
        ]);
    }

}