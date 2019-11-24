<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label' => 'Nom d\'utilisateur *',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 30,
                        'minMessage' => 'Votre nom d\'utilisateur doit comporter au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre nom d\'utilisateur ne doit pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('firstname', null, [
                'label' => 'Prénom *',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 30,
                        'minMessage' => 'Votre prénom doit comporter au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre prénom ne doit pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('lastname', null, [
                'label' => 'Nom *',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 30,
                        'minMessage' => 'Votre nom doit comporter au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre nom ne doit pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Password *'
                ],
                'second_options' => [
                    'label' => 'Password (confirmation) *'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
                        'message' => '8 à 20 caractères, au moins une majuscule, une minuscule et un chiffre.'
                    ])
                ],
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'first_options'  => [
                    'label' => 'Email *'
                ],
                'second_options' => [
                    'label' => 'Email (confirmation) *'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('addressNumber', null, [
                'label' => 'N° *',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 1,
                        'max' => 15,
                        'minMessage' => 'Ce champ ne doit pas être vide',
                        'maxMessage' => 'Ce champ ne doit pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('address1', null, [
                'label' => 'Voie, Rue... *',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 1,
                        'max' => 255,
                        'minMessage' => 'Ce champ ne doit pas être vide',
                        'maxMessage' => 'Ce champ ne doit pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('address2', null, [
                'label' => 'Voie, Rue... (2)',
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Ce champ ne doit pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])

            ->add('zipcode', null, [
                'label' => 'Code postal *',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 1,
                        'max' => 20,
                        'minMessage' => 'Ce champ ne doit pas être vide',
                        'maxMessage' => 'Ce champ ne doit pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('city', null, [
                'label' => 'Ville *',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 1,
                        'max' => 50,
                        'minMessage' => 'Ce champ ne doit pas être vide',
                        'maxMessage' => 'Ce champ ne doit pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('country', null, [
                'label' => 'Pays *',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 1,
                        'max' => 30,
                        'minMessage' => 'Ce champ ne doit pas être vide',
                        'maxMessage' => 'Ce champ ne doit pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Date de naissance *',
                'placeholder' => [
                    'year' => 'Année',
                    'month' => 'Mois',
                    'day' => 'Jour',
                ]
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Avatar',
                'data_class' => null,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User',
            'attr' => array('novalidate' => 'novalidate'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
