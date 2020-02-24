<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProjectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null,
            [
                'label' => 'Titre',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Ce champs est obligatoire'
                    ]),
                ]
            ])
//            ->add('styles', null,
//            [
//                'label' => 'Styles',
//                'expanded' => true
//            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Project',
            'attr' => array('novalidate' => 'novalidate'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_project';
    }


}
