<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Form\NewGroupFormType;
use App\Form\NewGroupMemberFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController {

    /**
     * Redirect the user to their list of groups
     * If there is no logged user, the website is redirected to the login page.
     *
     * @Route("/groups", name="groups")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function groups(Request $request) {
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $group = new Group();
        $group->setOwner($this->getUser());

        $form = $this->createForm(NewGroupFormType::class, $group);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($group);
            $entityManager->flush();

            $this->addFlash('success', 'Group created');
            return $this->redirectToRoute('groups');
        }

        return $this->render('groups/groupList.html.twig', [
            'groups' => $this->getUser()->getGroups(),
            'newGroupForm' => $form->createView()
        ]);
    }

    /**
     * Redirects the user to the member list of one of their groups.
     * If there is no logged user, the website is redirected to the login page.
     * If the user is not the group owner, the website is redirected to the error page, with error 401.
     *
     * @Route("/group/{groupId}", name="single_group")
     * @param Request $request
     * @param $groupId
     * @return RedirectResponse|Response
     */
    public function singleGroup(Request $request, $groupId){
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $group = $this
            ->getDoctrine()
            ->getRepository(Group::class)
            ->find($groupId);

        if ($group->getOwner() != $this->getUser())
            return $this->redirectToRoute('error', 401);

        $form = $this->createForm(NewGroupMemberFormType::class, $group);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $users = new ArrayCollection();

            $friends = $form->get('memberFriend')->getData();
            $emails =  $form->get('memberMail')->getData();

            if ($friends != "" || $emails != "") {
                $failures = 0;

                $userRepo = $this->getDoctrine()->getRepository(User::class);

                if ($emails != ""){
                    $emails = explode(',', $emails);
                    foreach ($emails as $email){
                        $user = $userRepo->findOneBy(['email' => $email]);
                        if ($user && !$group->getUser()->contains($user))
                            $users->add($user);
                        else
                            $failures++;
                    }
                }

                if ($friends != ""){
                    foreach ($friends as $friend){
                        if (!$group->getUser()->contains($friend))
                            $users->add($friend);
                        else
                            $failures++;
                    }
                }

                foreach ($users as $user)
                    $group->addUser($user);

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($group);
                $entityManager->flush();

                $this->addFlash('success', 'Members added.');
                if ($failures > 0)
                    $this->addFlash('warning',
                        $failures . " member" . ($failures > 1 ? 's':'') . " could not be added to the group.");
            } else
                $this->addFlash('danger', 'You have to add at least one member.');


            return $this->redirectToRoute('single_group', ['groupId' => $group->getId()]);
        }

        return $this->render('groups/groupDetail.html.twig', [
            'group' => $group,
            'newMemberForm' => $form->createView()
        ]);
    }

    /**
     * Takes a group created by the user and deletes it.
     * If there is no logged user, the website is redirected to the login page.
     * If the user is not the group owner, the website is redirected to the error page, with error 404.
     *
     * @Route("/groups/delete/{groupId}", name="group_delete")
     * @param $groupId
     * @return RedirectResponse
     */
    public function deleteGroup($groupId = 0){
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $group = $this->getDoctrine()
                    ->getRepository(Group::class)
                    ->find($groupId);

        if (!$group || $group->getOwner() != $this->getUser())
            return $this->redirectToRoute('error', ['errorCode' => 404]);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($group);
        $entityManager->flush();

        $this->addFlash('success', 'The group ' . $group->getName() . ' was successfully removed');

        return $this->redirectToRoute('groups');
    }

    /**
     * Takes a member from one of the user´s groups and removes it from said group.
     * If there is no logged user, the website is redirected to the login page.
     * If the user is not the group owner, the website is redirected to the error page, with error 404.
     *
     * @Route("/group/{groupId}/delete/{userId}", name="member_delete")
     * @param int $groupId
     * @param int $userId
     * @return RedirectResponse
     */
    public function deleteGroupMember($groupId = 0, $userId = 0){
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $group = $this->getDoctrine()
            ->getRepository(Group::class)
            ->find($groupId);

        if (!$group || $group->getOwner() != $this->getUser())
            return $this->redirectToRoute('error', ['errorCode' => 404]);

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        if (!$user || !$group->getUser()->contains($user))
            return $this->redirectToRoute('error', ['errorCode' => 404]);

        $group->removeUser($user);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($group);
        $entityManager->flush();

        $this->addFlash('success', $user->getUsername() . ' was removed from the group '. $group->getName());

        return $this->redirectToRoute('single_group', ['groupId' => $groupId]);
    }

}