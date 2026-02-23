<?php

namespace App\Form;

use App\Entity\ServiceMedical;
use App\Entity\Utilisateur;
use App\Enum\StatutUtilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('username', TextType::class)

            // ✅ mot de passe saisi en clair (pas stocké directement)
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => $options['is_new'], // obligatoire à la création
                'attr' => ['autocomplete' => 'new-password'],
                'label' => 'Mot de passe',
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutUtilisateur::class,
                'choice_label' => fn(StatutUtilisateur $s) => $s->label(),
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Médecin' => 'ROLE_MEDECIN',
                    'Infirmier(ère)' => 'ROLE_INFIRMIER',
                    'Réception' => 'ROLE_RECEPTION',
                    'RH' => 'ROLE_RH',
                ],
                'multiple' => true,
                'expanded' => true, 
            ])
            ->add('serviceMedical', EntityType::class, [
                'class' => ServiceMedical::class,
                'choice_label' => 'libelle', // remplace par ton vrai champ: "nom", "libelle", etc.
                'placeholder' => 'Choisir...',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'is_new' => true, // option custom
        ]);

        $resolver->setAllowedTypes('is_new', 'bool');
    }
}
