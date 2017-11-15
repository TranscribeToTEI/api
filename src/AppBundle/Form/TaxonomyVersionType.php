<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonomyVersionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('versionId',          IntegerType::class,                                   array('required' => true))
            ->add('taxonomyType',       TextType::class,                                      array('required' => true))
            ->add('reviewBy',           \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array('class' => 'UserBundle:User', 'required' => false))
            ->add('testator',           \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array('class' => 'AppBundle:Testator', 'required' => false))
            ->add('place',              \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array('class' => 'AppBundle:Place', 'required' => false))
            ->add('militaryUnit',       \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array('class' => 'AppBundle:MilitaryUnit', 'required' => false))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TaxonomyVersion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_taxonomyversion';
    }


}
