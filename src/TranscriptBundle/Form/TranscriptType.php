<?php

namespace TranscriptBundle\Form;

use Symfony\Component\Form\AbstractType;
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
            ->add('content',    TextType::class, array("required" => true))
            ->add('resource',   \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array("required" => true, 'class' => 'DataBundle:Resource'))
            ->add('createUser', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array("required" => false, 'class' => 'UserBundle:User'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TranscriptBundle\Entity\Transcript',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'transcriptbundle_transcript';
    }


}
