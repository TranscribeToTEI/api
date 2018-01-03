<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MilitaryUnitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',                       TextType::class,        array("required" => true))
            ->add('country',                    TextType::class,        array("required" => false))
            ->add('armyCorps',                  TextType::class,        array("required" => false))
            ->add('regimentName',               TextType::class,        array("required" => false))
            ->add('regimentNumber',             TextType::class,        array("required" => false))
            ->add('description',                TextareaType::class,    array("required" => false))
            ->add('updateComment',              TextType::class,        array("required" => true))
            ->add('isOfficialVersion',          TextType::class,        array("required" => false))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MilitaryUnit',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_militaryunit';
    }


}
