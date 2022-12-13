<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'fullName',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        //'placeholder' => 'Full Name',
                        'class' => 'form-control textarea-form-control',
                    ],
                    'label' => 'Full Name',
                    //'translation_domain' => 'messages',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                    'attr' => [
                        //'placeholder' => 'Email',
                        'class' => 'form-control textarea-form-control',
                    ],
                    'label' => 'Email',
                    //'translation_domain' => 'messages',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
