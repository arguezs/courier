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

        if (!$message)
            return $this->redirectToRoute('error', [ 'errorCode' => 404 ]);

        $inboxRepo = $this->getDoctrine()->getRepository(Inbox::class);
        $inbox = $inboxRepo->findBy([
            'user' => $this->getUser(),
            'message' => $message
        ]);

        if (!$inbox)
            return $this->redirectToRoute('error', [ 'errorCode' => 401 ]);

        foreach ($inbox as $row){
            if (!$row->getIsRead()) {
                $row->setIsRead(true);
                $this->getDoctrine()->getManager()->persist($row);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $this->render('message/message.html.twig', [
            'message' => $message
        ]);
    }

}