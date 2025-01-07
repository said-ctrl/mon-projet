<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Reproduction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReproductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la reproduction',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de reproduction',
                'choices' => [
                    'Impression' => 'impression',
                    'Copie' => 'copie',
                    'Autre' => 'autre',
                ],
            ])
           
            ->add('product', EntityType::class, [
                'class' => Product::class, // Associe avec l'entité Product
                'choice_label' => 'title', // Affiche le titre du produit dans la liste déroulante
                'label' => 'Produit associé',
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
            ])
            ;
    }


public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
    'data_class' => Reproduction::class,
]);
}
}
