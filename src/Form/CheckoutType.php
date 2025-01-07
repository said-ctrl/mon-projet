<?php

namespace App\Form;

use App\Entity\identifiant;
use App\Entity\Order;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('status')
            ->add('createdAt', null, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('shippingAdress')
            ->add('paymentMethod')
            ->add('identifiant', EntityType::class, [
                'class' => identifiant::class,
                'choice_label' => 'nom',  // Afficher le nom d'utilisateur, par exemple
                'query_builder' => function (EntityRepository $er)use ($user)  {
                    // Récupérer l'identifiant de l'utilisateur actuellement connecté
                    return $er->createQueryBuilder('i')
                        ->where('i.id = :userId')  // Filtrer l'identifiant de l'utilisateur connecté
                        ->setParameter('userId', $user->getId());
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'user'=> null,
        ]);
    }
}
