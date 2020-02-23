<?php

namespace App\Form;

use App\Entity\Friendship;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewFriendFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('receiver', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email address',
                    'class' => 'form-control my-3'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send request',
                'attr' => [
                    'class' => 'btn btn-secondary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(['data_class' => Friendship::class]);
    }

}