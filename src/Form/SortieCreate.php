<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\GreaterThan;

class SortieCreate extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'error_bubbling' => true,
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'error_bubbling' => true,
                'constraints' => [
                        new GreaterThan([
                        'propertyPath' => 'parent.all[dateLimiteInscription].data'
                    ])
                ]
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text',
                'error_bubbling' => true,
                'constraints' => [
                    new LessThan([
                        'propertyPath' => 'parent.all[dateHeureDebut].data'
                    ])
                ]
            ])
            ->add('duree', DateIntervalType::class, [
                'error_bubbling' => true,
                'widget' => 'choice',
                'with_years'  => false,
                'with_months' => true,
                'with_days'   => true,
                'with_hours'  => true,
                'with_minutes'  => true,
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'error_bubbling' => true,
            ])
            ->add('infosSortie', TextareaType::class, [
                'error_bubbling' => true,
            ])
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
