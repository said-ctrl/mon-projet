<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('email', TextType::class)
            ->add('description', TextareaType::class)
            ->add('preferences', TextType::class)
            ->add('type_oeuvre', ChoiceType::class, [
                'choices' => [
                    'Peinture' => 'peinture',
                    'Portrait' => 'portrait',
                    'Sculpture' => 'sculpture',
                    'Autre' => 'autre',
                ],
                'required' => false,  // Le champ "type_oeuvre" est optionnel
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
