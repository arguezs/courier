<?php

namespace App\Controller;

use App\Entity\Inbox;
use App\Form\NewMessageFormType;
use App\Repository\InboxRepository;
use phpDocumentor\Reflection\Types\This;
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

        $repository = $this
            ->getDoctrine()
            ->getRepository(Inbox::class);

        return $this->render('inbox/inbox.html.twig', [
            'messages' => $repository->findReceivedMessages($this->getUser()),
            'sent' => false,
            'newMessageForm' => $this->newMessageForm()->createView()
        ]);
    }

    /**
     * @Route("/inbox/sent", name="outbox")
     * @return RedirectResponse|Response
     */
    public function outbox(){
        if (!$this->getUser())
            return $this->redirectToRoute('home');

        $repository = $this->getDoctrine()->getRepository(Inbox::class);

        return $this->render('inbox/inbox.html.twig', [
            'messages' => $repository->findSentMessages($this->getUser()),
            'sent' => true,
            'newMessageForm' => $this->newMessageForm()->createView()
        ]);
    }

    public function newMessageForm(){
        $form = $this->createForm(NewMessageFormType::class);

        return $form;
    }

}