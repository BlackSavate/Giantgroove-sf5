<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TrackType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,
                [
                    'label' => 'Nom',
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Ce champs est obligatoire'
                        ])
                    ]
                ])
            ->add('isOpen', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'label' => false,
                'choices' => [
                    'Ouvert aux contributions' => true,
                    'Fichier audio' => false,
                ],
                'data' => 1
            ])
//            ->add('sheet', FileType::class,
//                [
//                    'label' => 'Partition',
//                    'data_class' => null
//                ])
//            ->add('instruments', null,
//                [
//                    'label' => 'Instruments',
//                    'expanded' => true,
//                ])
            ->add('audio', FileType::class,
                [
                    'label' => 'Audio',
                    'data_class' => null,
                ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Track',
            'attr' => ['novalidate' => 'novalidate'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_track';
    }


}
