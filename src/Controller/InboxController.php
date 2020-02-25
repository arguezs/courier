<?php

namespace App\Controller;

use App\Entity\Inbox;
use App\Entity\Message;
use App\Entity\User;
use App\Form\NewMessageFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InboxController extends AbstractController {

    /**
     * @Route("/inbox/{currentPage}", name="inbox")
     * @param Request $request
     * @param int $currentPage
     * @return RedirectResponse|Response
     */
    public function inbox(Request $request, $currentPage = 1){
        if (!$this->getUser())
            return $this->redirectToRoute('home');

        return $this->getInbox($request, $currentPage);
    }

    /**
     * @Route("/sent/{currentPage}", name="outbox")
     * @param Request $request
     * @param int $currentPage
     * @return RedirectResponse|Response
     */
    public function outbox(Request $request, $currentPage = 1){
        if (!$this->getUser())
            return $this->redirectToRoute('home');

        return $this->getInbox($request, $currentPage, true);
    }

    public function getInbox(Request $request, $currentPage, $sent = false, $limit = 20) {

        $messages = $this
            ->getDoctrine()
            ->getRepository(Inbox::class)
            ->getMessages($this->getUser(), $currentPage, $limit, $sent);

        $maxPages = ceil($messages['paginator']->count() / $limit);

        $message = new Message();
        $message->setSender($this->getUser());

        $form = $this->createForm(NewMessageFormType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $emails = explode(',', $form->get('forEmail')->getData());
            $friends = $form->get('forFriends')->getData();
            $groups = $form->get('forGroups')->getData();

            if ($friends->count() != 0 || $groups->count() != 0 || $emails[0]){
                $users = new ArrayCollection();
                $failures = 0;

                if ($friends->count() != 0)
                    foreach ($friends as $friend)
                        $users->add($friend);

                if ($groups->count() != 0)
                    foreach ($groups as $group)
                        foreach ($group->getUser() as $user)
                            if (!$users->contains($user))
                                $users->add($user);

                if ($emails[0]){
                    $userRepo = $this->getDoctrine()->getRepository(User::class);
                    foreach ($emails as $email){
                        $user = $userRepo->findOneBy(['email' => $email]);
                        if ($user && !$users->contains($user))
                            $users->add($user);
                        elseif (!$user)
                            $failures++;
                    }
                }

                if ($failures == count($emails) && $friends == '' && $groups  == '')
                    $this->addFlash('danger', 'No valid users were given.');
                else{
                    foreach ($users as $user)
                        $message->addReceiver($user);

                    $message->setDate(new \DateTime('NOW'));

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($message);
                    $entityManager->flush();

                    $this->addFlash('success', 'Message sent');
                    if ($failures > 0)
                        $this->addFlash('warning',
                        'Some given users were not valid, and they won´t receive the message.');

                    if ($sent)
                        $this->redirectToRoute('outbox');
                    else
                        $this->redirectToRoute('inbox');
                }
            } else
                $this->addFlash('danger', 'You must add at least one recipient');
        }

        return $this->render('inbox/inbox.html.twig', [
            'inbox' => $messages['paginator'],
            'maxPages' => $maxPages,
            'thisPage' => $currentPage,
            'sent' => $sent,
            'newMessageForm' => $form->createView()
        ]);
    }

}