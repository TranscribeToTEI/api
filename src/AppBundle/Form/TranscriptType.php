<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranscriptType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content',                    TextareaType::class,                                    array("required" => false))
            ->add('status',                     TextType::class,                                        array("required" => false, "description" => "Allowed values: \"todo\", \"transcription\", \"validation\", \"validated\""))
            ->add('updateComment',              TextType::class,                                        array("required" => false))
            ->add('continueBefore',             CheckboxType::class,                                    array("required" => false))
            ->add('continueAfter',              CheckboxType::class,                                    array("required" => false))
            ->add('submitUser',                 \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => false, 'class' => 'UserBundle:User'))
            ->add('validationText',             TextareaType::class,                                    array("required" => false))
            ->add('sendNotification',           CheckboxType::class,                                    array("required" => false, "mapped" => false))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Transcript',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_transcript';
    }


}
