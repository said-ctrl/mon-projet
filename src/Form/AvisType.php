<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
            ])
           
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'entité concernée',
                'choices' => [
                    'Demande de Conseil' => 'DemandeConseil',
                    'Demande de Création' => 'DemandeCreation',
                    'Reproduction' => 'Reproduction',
                ],
            ]);
            if (!$options['is_logged_in']) { // Ajoute l'email uniquement si l'utilisateur n'est pas connecté
                $builder->add('email', EmailType::class, [
                    'label' => 'Votre email',
                ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
