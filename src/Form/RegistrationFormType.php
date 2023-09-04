<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Partie Utilisateur de l\'Email',
                'attr' => [
                    'placeholder' => 'ex: mon.mail',
                ],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[^@]+$/',
                        'message' => 'La partie utilisateur de l\'email ne doit pas contenir le caractère "@".',
                    ]),
                ],
            ])
            ->add('emailDomain', ChoiceType::class, [
                'mapped' => false,
                'label' => "Domaine de l'adresse mail",
                'choices' => [
                    '@insider.fr' => 'insider.fr',
                    '@collaborator.fr' => 'collaborator.fr',
                    '@external.fr' => 'external.fr',
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Votre Nom d\'utilisateur',
                'attr' => [
                    'placeholder' => 'ex: mon_nom_utilisateur',
                ]
            ])
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Votre mot de passe',
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'ex: d*!M9afG87'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe dois comporter au moins {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Accepter les conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'vous devez accepter les termes avant de pouvoir valider le formulaire.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
