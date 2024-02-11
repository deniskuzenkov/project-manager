<?php

namespace App\ReadModel\Work\Members\Member\Filter;

use App\Model\Work\Entity\Members\Member\Status;
use App\ReadModel\Work\Members\GroupFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $groups;

    public function __construct(GroupFetcher $groups)
    {
        $this->groups = $groups;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Name',
                    'onchange' => 'this.form.submit()',
                ]
            ])
            ->add('email', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Email',
                    'onchange' => 'this.form.submit()'
                ]
            ])
            ->add('group', ChoiceType::class, [
                'choices' => array_flip($this->groups->assoc()),
                'required' => false,
                'placeholder' => 'All groups',
                'attr' => ['onchange' => 'this.form.submit()']
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                   'Active' => Status::ACTIVE,
                    'Archived' => Status::ARCHIVED
                ],
                'required' => false,
                'placeholder' => 'All statuses',
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