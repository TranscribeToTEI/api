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
            // Voir https://stackoverflow.com/questions/44952373/creating-new-entity-associate-with-an-existing-entity-in-entitytype-field
            //->add('name',                       PlaceNameType::class,   array('required' => true))
            ->add('name',                       CollectionType::class, array(
                "required" => false,
                "allow_add" => true,
                "allow_delete" => true,
                "delete_empty" => true,
                'entry_type'   => PlaceNameType::class
            ))
            ->add('frenchDepartement',          PlaceNameType::class,   array('required' => false))
            ->add('frenchRegion',               PlaceNameType::class,   array('required' => false))
            ->add('city',                       PlaceNameType::class,   array('required' => false))
            ->add('country',                    PlaceNameType::class,   array('required' => false))
            ->add('description',                TextareaType::class,    array("required" => false))
            ->add('geonamesId',                 TextType::class,        array("required" => false))
            ->add('geographicalCoordinates',    TextType::class,        array("required" => false))
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
