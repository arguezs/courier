<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => false
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'label' => 'Avatar',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '512k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Image must be JPG or PNG',
                        'maxSizeMessage' => 'Image must be max 512Kb'
                    ])
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'mapped' => false,
                'required' => false,
                'type' => PasswordType::class,
                'first_options' =>[
                    'label' => false,
                    'attr' => [ 'placeholder' => 'New password' ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [ 'placeholder' => 'Repeat new password' ]
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => false,
                'mapped' => false,
                'attr' => ['placeholder' => 'Password']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update profile',
                'attr' => ['class' => 'btn-secondary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(['data_class' => User::class]);
    }

}