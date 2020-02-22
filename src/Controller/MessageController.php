<?php

namespace App\Controller;

use App\Entity\Inbox;
use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController {

    /**
     * @Route("/message/{id}", name="message")
     * @return RedirectResponse|Response
     */
    public function message($id){
        if (!$this->getUser())
            return $this->redirectToRoute('home');

        $messageRepo = $this->getDoctrine()->getRepository(Message::class);
        $message = $messageRepo->find($id);

        $inboxRepo = $this->getDoctrine()->getRepository(Inbox::class);
        $inbox = $inboxRepo->findOneBy([
            'user' => $this->getUser(),
            'message' => $message
        ]);

        if (!$inbox)
            return $this->redirectToRoute('home');

        return $this->render('message/message.html.twig', [
            'message' => $message
        ]);
    }

}