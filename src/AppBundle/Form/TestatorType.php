<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestatorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',               TextType::class, array("required" => true))
            ->add('surname',            TextType::class, array("required" => true))
            ->add('firstnames',         TextType::class, array("required" => true))
            ->add('profession',         TextType::class, array("required" => false))
            ->add('address',            TextType::class, array("required" => true))
            ->add('dateOfBirth',        DateType::class, array("required" => true, 'format' => 'yyyy-MM-dd', 'widget' => 'single_text'))
            ->add('placeOfBirth',       TextType::class, array("required" => true))
            ->add('dateOfDeath',        DateType::class, array("required" => true, 'format' => 'yyyy-MM-dd', 'widget' => 'single_text'))
            ->add('placeOfDeath',       TextType::class, array("required" => true))
            ->add('deathMention',       TextType::class, array("required" => true))
            ->add('memoireDesHommes',   UrlType::class, array("required" => true))
            ->add('regiment',           TextType::class, array("required" => false))
            ->add('rank',               TextType::class, array("required" => false))
            ->add('description',        TextareaType::class, array("required" => false))
            ->add('updateComment',      TextType::class,        array("required" => true))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Testator',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_testator';
    }


}
