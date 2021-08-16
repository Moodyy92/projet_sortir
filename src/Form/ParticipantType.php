<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('pseudo',TextType::class,[

                'label'=>'Pseudo : ',
            ])
            ->add('nom',TextType::class,[

                'label'=>'Nom : ',

                'required'=>true
            ])
            ->add('prenom',TextType::class,[

                'label'=>'Prenom : ',
                'required'=>true
            ])
            ->add('telephone',TelType::class,[

                'label'=>'Telephone : ',
                'required'=>true

            ])
            ->add('campus',EntityType::class,[

                'required'=>true,
                'label'=>'Campus : ',
                'class'=>Campus::class,
                'choice_label'=>'nom'
            ])
            ->add('email',TextType::class,[

                'label'=>'Email : ',
                'required'=>true
            ])
            ->add('password',PasswordType::class, [

                'label'=>'Mot de passe : ',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe ',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir un minimun de {{ limit }} characteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])



        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
