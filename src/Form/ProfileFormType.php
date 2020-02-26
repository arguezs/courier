<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => $builder->getData()->getName(),
                    'class' => 'form-control my-3'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['class' => 'form-control my-3']
            ])
            ->add('password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' =>[
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Password',
                        'class' => 'form-control my-3'
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control my-3',
                        'placeholder' => 'Repeat password'
                    ]
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update profile',
                'attr' => ['class' => 'btn btn-secondary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(['data_class' => User::class]);
    }

}