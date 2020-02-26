<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController {

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function profile(Request $request, UserPasswordEncoderInterface $encoder){
        if (!$this->getUser())
            return $this->redirectToRoute('sign_in');

        $form = $this->createForm(ProfileFormType::class, $this->getUser());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $password = $form->get('password')->getData();

            if ($encoder->isPasswordValid($this->getUser(), $password)){
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($this->getUser());
                $entityManager->flush();

                $this->addFlash('success', 'Profile updated');
            } else
                $this->addFlash('danger', 'Password incorrect');

            return $this->redirectToRoute('profile');
        }

        return $this->render('profile.html.twig', [
            'profileForm' => $form->createView()
        ]);
    }

}