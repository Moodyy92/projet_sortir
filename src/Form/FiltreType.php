<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus :',
            ])
            ->add('contient', TextType::class, [
                'label'=> 'Le nom de la sortie contient :',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
            ])
            ->add('choices', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'Sorties dont je suis l\'organisateur/trice'=>'orga',
                    'Sorties auxquelles je suis inscrit/e'=>'inscrit',
                    'Sorties auxquelles je ne suis pas inscrit/e'=>'noninscrit',
                    'Sorties passées'=>'passees',
                ],
                'expanded'=>true,
                'row_attr'=>['class' => 'd-flex flex-column'],
                'label'=>''
            ])
        ->add('dateDeb', DateType::class, [
            'label'=> 'Entre',
            'widget' => 'single_text',
            'html5' => true,
            'attr' => ['class' => 'js-datepicker'],
            'required'=>false,
        ])
        ->add('dateFin', DateType::class, [
            'label'=> 'et ',
            'widget' => 'single_text',
            'html5' => true,
            'attr' => ['class' => 'js-datepicker'],
            'required'=>false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
