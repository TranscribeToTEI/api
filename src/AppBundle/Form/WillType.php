<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WillType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('callNumber',         TextType::class, array("required" => true))
            ->add('title',              TextType::class, array("required" => false))
            ->add('minuteDate',         DateType::class, array("required" => true, 'format' => 'yyyy-MM-dd', 'widget' => 'single_text'))
            ->add('willWritingDate',    DateType::class, array("required" => true, 'format' => 'yyyy-MM-dd', 'widget' => 'single_text'))
            ->add('willWritingPlace',   \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array("required" => false, 'class' => 'AppBundle:Place'))
            ->add('entity',             \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array("required" => false, 'class' => 'AppBundle:Entity'))
            ->add('testator',           \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array("required" => true, 'class' => 'AppBundle:Testator'))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Will',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_will';
    }


}
