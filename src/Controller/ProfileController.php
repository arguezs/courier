<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

            /** @var UploadedFile $avatar */
            $avatar = $form->get('avatar')->getData();

            if ($encoder->isPasswordValid($this->getUser(), $password)){

                if ($avatar){
                    $originalName = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalName);
                    $newFilename = $safeFilename . '-' . uniqid() . $avatar->guessExtension();

                    try {
                        $avatar->move(
                            $this->getParameter('avatar_directory'),
                            $newFilename
                        );
                    } catch (FileException $e){
                        $this->addFlash('danger', 'Failed to upload image');
                        return $this->redirectToRoute('profile');
                    }

                    $this->getUser()->setAvatar($newFilename);
                }

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