<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\NewGroupFormType;
use App\Form\NewGroupMemberFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController {

    /**
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
     * @Route("/group/{groupId}", name="single_group")
     * @param $groupId
     * @return RedirectResponse|Response
     */
    public function singleGroup($groupId){
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $group = $this
            ->getDoctrine()
            ->getRepository(Group::class)
            ->find($groupId);

        if ($group->getOwner() != $this->getUser())
            return $this->redirectToRoute('error', 401);

        $form = $this->createForm(NewGroupMemberFormType::class, $group);

        return $this->render('groups/groupDetail.html.twig', [
            'group' => $group,
            'newMemberForm' => $form->createView()
        ]);
    }

}