<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewMessageFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('forEmail', EmailType::class, [
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'For',
                    'class' => 'form-control my-3'
                ],
                'label' => false
            ])
            ->add('about', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'About',
                    'class' => 'form-control my-3'
                ]
            ])
            ->add('body', TextareaType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Message',
                    'class' => 'form-control my-3'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send message',
                'attr' => [ 'class' => 'btn-secondary' ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Message::class
        ]);
    }

}