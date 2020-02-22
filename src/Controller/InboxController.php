<?php

namespace App\Controller;

use App\Entity\Inbox;
use App\Entity\Message;
use App\Entity\User;
use App\Form\NewMessageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InboxController extends AbstractController {

    /**
     * @Route("/inbox", name="inbox")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function inbox(Request $request){
        if (!$this->getUser())
            return $this->redirectToRoute('home');

        $repository = $this
            ->getDoctrine()
            ->getRepository(Inbox::class);

        return $this->render('inbox/inbox.html.twig', [
            'messages' => $repository->findReceivedMessages($this->getUser()),
            'sent' => false,
            'newMessageForm' => $this->newMessageForm($request)->createView()
        ]);
    }

    /**
     * @Route("/inbox/sent", name="outbox")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function outbox(Request $request){
        if (!$this->getUser())
            return $this->redirectToRoute('home');

        $repository = $this->getDoctrine()->getRepository(Inbox::class);

        return $this->render('inbox/inbox.html.twig', [
            'messages' => $repository->findSentMessages($this->getUser()),
            'sent' => true,
            'newMessageForm' => $this->newMessageForm($request)->createView()
        ]);
    }

    public function newMessageForm(Request $request){
        $msg = new Message();
        $msg->setSender($this->getUser());
        $form = $this->createForm(NewMessageFormType::class, $msg);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $mails = explode(',', $form->get('forEmail')->getData());
            $userRepo = $this->getDoctrine()->getRepository(User::class);

            foreach ($mails as $mail){
                $user = $userRepo->findOneBy(['email' => $mail]);

                if ($user)
                    $msg->addReceiver($user);
            }

            $msg->setDate(new \DateTime('NOW'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($msg);
            $entityManager->flush();

            $this->addFlash('success', 'Message sent');
        }

        return $form;
    }

}