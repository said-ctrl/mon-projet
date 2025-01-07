<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
        ->add('title')
        ->add('content')
        ->add('product', EntityType::class, [
            'class' => Product::class,
            'query_builder' => function ($repository) use ($user) {
                // Filtrer les produits pour l'utilisateur connecté
                return $repository->createQueryBuilder('p')
                    ->where('p.user = :user')
                    ->setParameter('user', $user) // Utiliser l'utilisateur passé en option
                    ->orderBy('p.title', 'ASC');
            },
            'choice_label' => 'title', // Afficher le titre du produit dans le formulaire
            'attr' => ['class' => 'text-black border px-4 py-2 w-full rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500']
        ])
    ;
}

public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        'data_class' => Article::class,
        'user' => null,  // Ajoutez 'user' comme option ici
    ]);
}
}
