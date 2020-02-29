<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    /**
     * If there is no logged user, the home page is shown.
     *
     * If there is a logged user, then they are redirected to their main inbox.
     * @Route("/", name="home")
     * @return RedirectResponse|Response
     */
    public function home() {
        if ($this->getUser())
            return $this->redirectToRoute('inbox');

        return $this->render('home.html.twig');
    }

}