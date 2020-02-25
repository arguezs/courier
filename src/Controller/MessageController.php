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
     * @param $id
     * @return RedirectResponse|Response
     */
    public function message($id){
        if (!$this->getUser())
            return $this->redirectToRoute('home');

        $message = $this->isVisible($id);

        if (!$message)
            return $this->redirectToRoute('error', 404);

        return $this->render('message/message.html.twig', [
            'message' => $message
        ]);
    }

    /**
     * @Route("/message/{messageId}/delete", name="delete_msg")
     * @param $messageId
     * @return RedirectResponse
     */
    public function deleteMessage($messageId){
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $message = $this->isVisible($messageId);

        if (!$message)
            return $this->redirectToRoute('error', 404);

        $inbox = $this->getDoctrine()
            ->getRepository(Inbox::class)
            ->findBy([
                'user' => $this->getUser(),
                'message' => $message
            ]);

        $entityManager = $this->getDoctrine()->getManager();

        foreach ($inbox as $link){
            $entityManager->remove($link);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Message was removed');
        return $this->redirectToRoute('inbox');
    }

    private function isVisible($messageId){
        $messageRepo = $this->getDoctrine()->getRepository(Message::class);
        $message = $messageRepo->find($messageId);

        if (!$message)
            return false;

        $inboxRepo = $this->getDoctrine()->getRepository(Inbox::class);
        $inbox = $inboxRepo->findBy([
            'user' => $this->getUser(),
            'message' => $message
        ]);

        if (!$inbox)
            return false;

        foreach ($inbox as $row){
            if (!$row->getIsRead()) {
                $row->setIsRead(true);
                $this->getDoctrine()->getManager()->persist($row);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $message;
    }

}