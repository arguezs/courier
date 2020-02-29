<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class NewGroupMemberFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('memberMail', EmailType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Email address',
                    'multiple' => true
                ]
            ])
            ->add('memberFriend', EntityType::class, [
                'class' => User::class,
                'label' => false,
                'mapped' => false,
                'multiple' => true,
                'choices' => $builder->getData()->getOwner()->getFriends(),
                'group_by' => function() {return 'Friends';},
                'choice_label' => 'name'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Add members',
                'attr' => ['class' => 'btn-secondary']
            ]);
    }

}