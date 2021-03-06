<?php

namespace App\Controller;

use App\Entity\Inbox;
use App\Entity\Message;
use App\Form\MessageReplyFormType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController {

    /**
     * Gets and presents the body of a single message, provided the user has access to it.
     *
     * @Route("/message/{messageId}", name="message")
     * @param $messageId the message ID
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function message($messageId = 0, Request $request){
        if (!$this->getUser())
            return $this->redirectToRoute('home');

        $message = $this->isVisible($messageId);

        if (!$message)
            return $this->redirectToRoute('error', 404);

        $reply = new Message();
        $reply->setSender($this->getUser());
        $reply->addReceiver($message->getSender());
        $reply->setAbout(preg_match("/^RE:/", $message->getAbout()) ? '' : 'RE: ' . $message->getAbout());

        $form = $this->createForm(MessageReplyFormType::class, $reply);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $reply->setDate(new \DateTime('NOW'));
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($reply);
            $entityManager->flush();

            $this->addFlash('success', 'Reply sent.');
            return $this->redirectToRoute('message', ['messageId' => $messageId]);
        }

        return $this->render('message/message.html.twig', [
            'message' => $message,
            'replyForm' => $form->createView()
        ]);
    }

    /**
     * Removes a message from the user´s inbox, provided the user has access to the message.
     *
     * @Route("/message/{messageId}/delete", name="delete_msg")
     * @param $messageId the message ID.
     * @return RedirectResponse
     */
    public function deleteMessage($messageId = 0){
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

    /**
     * Checks that a Message exists and that it exists at least one Inbox instance relating the Message to the logged User.
     *
     * @param $messageId the message ID.
     * @return Message|bool|object|null
     */
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
            if (!$row->isRead()) {
                $row->setIsRead(true);
                $this->getDoctrine()->getManager()->persist($row);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $message;
    }

}