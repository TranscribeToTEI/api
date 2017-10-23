<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainingContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',          TextType::class,        array("required" => true))
            ->add('pageType',       TextType::class,        array("required" => true))
            ->add('pageStatus',     TextType::class,        array("required" => true))
            ->add('content',        TextareaType::class,    array("required" => false))
            ->add('illustration',   UrlType::class,         array("required" => false))
            ->add('updateComment',  TextType::class,        array("required" => true))
            ->add('orderInTraining',IntegerType::class,     array("required" => false))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TrainingContent'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_trainingcontent';
    }


}
