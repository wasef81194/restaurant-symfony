<?php

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $role = 'ROLE_ADMIN';
        $builder
            ->add('nom')
            ->add('adresse')
            ->add('telephone')
            ->add('responsable', EntityType::class,[
                'class' => User::class,
                'choice_label' => 'email',
                'multiple' => true,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use($role){
                    return $er->createQueryBuilder('u')
                    ->where('u.roles LIKE :role')
                    ->setParameter(':role', '%"'.$role.'"%');
                }, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
