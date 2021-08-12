<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieCreate extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('duree', DateIntervalType::class, [
                'widget' => 'choice',
                'with_years'  => false,
                'with_months' => true,
                'with_days'   => true,
                'with_hours'  => true,
                'with_minutes'  => true,
            ])
            ->add('nbInscriptionMax')
            ->add('infosSortie')
            ->add('Publier', SubmitType::class)
            ->add('Creer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
