<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferenceItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entity',             \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:Entity'))
            ->add('printedReference',   \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:PrintedReference'))
            ->add('manuscriptReference',\Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:ManuscriptReference'))
            ->add('updateComment',      TextType::class,                                        array("required" => true))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ReferenceItem'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_referenceitem';
    }


}
