<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                'required' => false,
                'attr' => [
                    'placeholder' => 'For',
                    'class' => 'form-control my-3',
                    'multiple' => true
                ],
                'label' => false
            ])
            ->add('forFriends', EntityType::class, [
                'mapped' => false,
                'class' => User::class,
                'multiple' => true,
                'group_by' => function(){return 'Friends';},
                'choices' => $builder->getData()->getSender()->getFriends(),
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control my-3'
                ],
                'label' => false,
                'required' => false
            ])
            ->add('forGroups', EntityType::class, [
                'mapped' => false,
                'class' => Group::class,
                'multiple' => true,
                'group_by' => function(){return 'Groups';},
                'choices' => $builder->getData()->getSender()->getGroups(),
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control my-3'
                ],
                'label' => false,
                'required' => false
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
                'attr' => [ 'class' => 'btn btn-secondary' ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Message::class
        ]);
    }

}