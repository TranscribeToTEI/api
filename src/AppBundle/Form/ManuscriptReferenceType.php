<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManuscriptReferenceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('documentName',               TextType::class,        array("required" => false))
            ->add('institutionName',            TextType::class,        array("required" => true))
            ->add('collectionName',             TextType::class,        array("required" => false))
            ->add('documentNumber',             TextType::class,        array("required" => true))
            ->add('url',                        TextType::class,        array("required" => false))
            ->add('updateComment',              TextType::class,        array("required" => true))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ManuscriptReference'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_manuscriptreference';
    }


}
