<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\NewGroupFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController {

    /**
     * @Route("/groups", name="groups")
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

}