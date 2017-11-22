<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',          TextType::class,        array("required" => true))
            ->add('content',        TextareaType::class,    array("required" => true))
            ->add('abstract',       TextareaType::class,    array("required" => true))
            ->add('type',           TextType::class,        array("required" => true, "description" => "Allowed values: \"blogContent\", \"helpContent\", \"staticContent\""))
            ->add('status',         TextType::class,        array("required" => false))
            ->add('onHomepage',     CheckboxType::class,    array("required" => false))
            ->add('enableComments', CheckboxType::class,    array("required" => false))
            ->add('updateComment',  TextType::class,        array("required" => true))
            ->add('tags',           CollectionType::class,
                array(
                    'required' => false,
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                )
            )
            ->add('illustration',   TextType::class,        array("required" => false))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Content',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_content';
    }


}
