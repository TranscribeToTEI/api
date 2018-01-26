<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('title',                              TextType::class,        array("required" => true))
            ->add('internalGoal',                       TextareaType::class,    array("required" => true))
            ->add('editorialResponsibility',            CollectionType::class,  array(
                                                                                                'entry_type'   => \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,
                                                                                                'entry_options'  => array(
                                                                                                    'class' => 'UserBundle:User'
                                                                                                ),
                                                                                                'allow_add'  => true,
                                                                                                'allow_delete' => true,
                                                                                                "required" => false))
            ->add('pageType',                           TextType::class,        array("required" => true, "description" => "Allowed values: \"presentation\", \"exercise\""))
            ->add('pageStatus',                         TextType::class,        array("required" => true, "description" => "Allowed values: \"draft\", \"public\", \"notIndexed\""))
            ->add('updateComment',                      TextType::class,        array("required" => true))
            ->add('orderInTraining',                    IntegerType::class,     array("required" => false))
            ->add('content',                            TextareaType::class,    array("required" => true))
            ->add('illustration',                       TextType::class,        array("required" => false))
            ->add('videoContainer',                     TextareaType::class,    array("required" => false))
            ->add('exerciseHeader',                     TextareaType::class,    array("required" => false))
            ->add('exerciseImageToTranscribe',          TextType::class,        array("required" => false))
            ->add('exercisePreloadText',                TextareaType::class,    array("required" => false))
            ->add('exerciseIsSmartTEI',                 CheckboxType::class,    array("required" => false))
            ->add('exerciseIsAttributesManagement',     CheckboxType::class,    array("required" => false))
            ->add('exerciseTagsList',                   CollectionType::class,  array(
                                                                                                'entry_type'   => TextType::class,
                                                                                                'allow_add'  => true,
                                                                                                'allow_delete' => true,
                                                                                                "required" => false))
            ->add('exerciseIsLiveRender',               CheckboxType::class,    array("required" => false))
            ->add('exerciseIsHelp',                     CheckboxType::class,    array("required" => false))
            ->add('exerciseIsDocumentation',            CheckboxType::class,    array("required" => false))
            ->add('exerciseIsTaxonomy',                 CheckboxType::class,    array("required" => false))
            ->add('exerciseIsNotes',                    CheckboxType::class,    array("required" => false))
            ->add('exerciseIsVersioning',               CheckboxType::class,    array("required" => false))
            ->add('exerciseIsComplexFields',            CheckboxType::class,    array("required" => false))
            ->add('exerciseCorrectionTranscript',       TextareaType::class,    array("required" => false))
            ->add('exerciseCorrectionErrorsToAvoid',    TextareaType::class,    array("required" => false))
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
