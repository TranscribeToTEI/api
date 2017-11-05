<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('willNumber',                 IntegerType::class,     array("required" => true))
            ->add('will',                       WillType::class,        array("required" => false))
            ->add('resources',                  CollectionType::class,  array(
                                                                                        "required" => false,
                                                                                        "allow_add" => true,
                                                                                        "allow_delete" => true,
                                                                                        "delete_empty" => true,
                                                                                        'entry_type'   => ResourceType::class
                                                                                    ))
            ->add('isShown',                    TextType::class,        array("required" => true))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Entity',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_entity';
    }


}
