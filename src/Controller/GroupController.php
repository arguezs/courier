<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController {

    /**
     * @Route("/groups", name="groups")
     * @return RedirectResponse|Response
     */
    public function groups() {
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        return $this->render('groups/groupList.html.twig',
            [ 'groups' => $this->getUser()->getGroups() ]);
    }

}