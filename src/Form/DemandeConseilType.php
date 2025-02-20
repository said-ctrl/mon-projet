<?php

namespace App\Form;

use App\Entity\DemandeConseil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeConseilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('email', TextType::class)
            ->add('sujet', TextType::class)
            ->add('message', TextareaType::class)
            // Ajout du champ entreprise
            ->add('entreprise', TextType::class, [
                'required' => false, // Ce champ est optionnel
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DemandeConseil::class,
        ]);
    }
}
