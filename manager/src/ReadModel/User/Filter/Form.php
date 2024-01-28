<?php

namespace App\ReadModel\User\Filter;

use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Name',
                    'onchange' => 'this.form.submit()'
                ]
            ])
            ->add('email', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Email',
                    'onchange' => 'this.form.submit()'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Wait' => User::STATUS_WAIT,
                    'Active' => User::STATUS_ACTIVE,
                    'Blocked' => User::STATUS_BLOCKED
                ],
                'required'=> false,
                'placeholder' => 'All statuses',
                'attr' => [
                    'onchange' => 'this.form.submit()'
                ]
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'User' => Role::USER,
                    'Admin' => Role::ADMIN
                ],
                'required'=> false,
                'placeholder' => 'All roles',
                'attr' => [
                    'onchange' => 'this.form.submit()'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class' => Filter::class,
           'method' => 'GET',
           'csrf_protection' => false
        ]);
    }

}