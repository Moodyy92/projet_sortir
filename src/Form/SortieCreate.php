<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieCreate extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('dateLimiteInscription')
            ->add('duree', DateIntervalType::class, [
                'with_years'  => false,
                'with_months' => true,
                'with_days'   => true,
                'with_hours'  => true,
                'with_minutes'  => true,
            ])
            ->add('nbInscriptionMax')
            ->add('infosSortie')
            ->add('latitude', TextType::class, [ 'mapped' => false ])
            ->add('longitude', TextType::class, [ 'mapped' => false ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
