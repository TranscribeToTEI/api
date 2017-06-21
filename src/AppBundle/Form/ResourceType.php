<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type',       TextType::class, array("required" => true))
            ->add('orderInWill',TextType::class, array("required" => true))
            ->add('entity',     \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array("required" => true, 'class' => 'AppBundle:Entity'))
            ->add('transcript', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array("required" => true, 'class' => 'AppBundle:Transcript'))
            ->add('createUser', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array("required" => false, 'class' => 'UserBundle:User'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Resource',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_resource';
    }


}
