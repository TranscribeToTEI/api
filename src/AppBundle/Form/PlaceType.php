<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('indexName',                  TextType::class,        array("required" => false))
            ->add('name',                       TextType::class,        array("required" => false))
            ->add('frenchDepartement',          TextType::class,        array("required" => false))
            ->add('frenchRegion',               TextType::class,        array("required" => false))
            ->add('city',                       TextType::class,        array("required" => false))
            ->add('country',                    TextType::class,        array("required" => false))
            ->add('description',                TextareaType::class,    array("required" => false))
            ->add('geonamesId',                 TextType::class,        array("required" => false))
            ->add('geographicalCoordinates',    TextType::class,        array("required" => false))
            ->add('isOfficialVersion',          TextType::class,        array("required" => false))
            ->add('updateComment',              TextType::class,        array("required" => true))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Place',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_place';
    }


}
