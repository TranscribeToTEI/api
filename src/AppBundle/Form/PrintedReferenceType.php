<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintedReferenceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('authors',                    TextType::class,        array("required" => true))
            ->add('referenceTitle',             TextType::class,        array("required" => true))
            ->add('containerTitle',             TextType::class,        array("required" => false))
            ->add('containerType',              TextType::class,        array("required" => false, "description" => "Allowed values: \"Revue\", \"Monographie\", \"Ouvrage collectif\""))
            ->add('url',                        TextType::class,        array("required" => false))
            ->add('otherInformation',           TextType::class,        array("required" => false))
            ->add('updateComment',              TextType::class,        array("required" => true))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PrintedReference'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_printedreference';
    }


}
