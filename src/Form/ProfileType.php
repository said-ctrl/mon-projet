<?php

namespace App\Form;

use App\Entity\Identifiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
            ])
            ->add('codepostal', TextType::class, [
                'label' => 'Code Postal',
                'required' => false,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false,
                'empty_data' => '', 
                'constraints' => [
                new Length([
                    'min' => 4,
                    'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    'max' => 20,
                    'maxMessage' => 'Le mot de passe ne doit pas dépasser {{ limit }} caractères.',
                ]),
            ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Identifiant::class,
        ]);
    }
}
