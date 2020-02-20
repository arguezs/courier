<?php

namespace App\Controller;

use App\Repository\InboxRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InboxController extends AbstractController {

    /**
     * @Route("/inbox", name="inbox")
     * @return RedirectResponse|Response
     */
    public function inbox(){
        if (!$this->getUser())
            return $this->redirectToRoute('home');

        $repository = new InboxRepository();

        return $this->render('inbox/inbox.html.twig', [
            'messages' => $repository->findReceivedMessages($this->getUser())
        ]);
    }

}