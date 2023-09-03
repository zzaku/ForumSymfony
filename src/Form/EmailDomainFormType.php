<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailDomainFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emailDomain', ChoiceType::class, [
                'choices' => [
                    'Insider' => 'insider.fr',
                    'Collaborator' => 'collaborator.fr',
                    'External' => 'external.fr',
                ],
                'label' => 'Sélectionnez le rôle',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configurez ici les options par défaut du formulaire
        ]);
    }
}
