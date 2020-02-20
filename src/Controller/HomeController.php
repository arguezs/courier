<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    /**
     * @Route("/", name="home")
     * @return RedirectResponse|Response
     */
    public function home() {
        if ($this->getUser())
            return $this->redirectToRoute('inbox');

        return $this->render('home.html.twig');
    }

}