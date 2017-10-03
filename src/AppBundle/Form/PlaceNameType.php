<?php

namespace AppBundle\Form;

use Nelmio\ApiDocBundle\Tests\Fixtures\Form\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceNameType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',           TextType::class,        array("required" => true))
            ->add('date',           TextType::class,        array("required" => false))
            ->add('year',           TextType::class,        array("required" => false))
            ->add('placeType',      TextType::class,        array("required" => false))
            ->add('updateComment',  TextType::class,        array("required" => true))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PlaceName',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_placename';
    }


}
