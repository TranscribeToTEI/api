<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('name',               TextType::class,                                        array("required" => true))
            ->add('surname',            TextType::class,                                        array("required" => true))
            ->add('firstnames',         TextType::class,                                        array("required" => true))
            ->add('profession',         TextType::class,                                        array("required" => false))
            ->add('addressNumber',      TextType::class,                                        array("required" => false))
            ->add('addressStreet',      TextType::class,                                        array("required" => false))
            ->add('addressDistrict',    TextType::class,                                        array("required" => false))
            ->add('addressCity',        TextType::class,                                        array("required" => false))
            ->add('dateOfBirth',        TextType::class,                                        array("required" => true))
            ->add('yearOfBirth',        TextType::class,                                        array("required" => true))
            ->add('placeOfBirth',       \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:Place'))
            ->add('dateOfDeath',        TextType::class,                                        array("required" => true))
            ->add('yearOfDeath',        TextType::class,                                        array("required" => true))
            ->add('placeOfDeath',       \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:Place'))
            ->add('deathMention',       TextType::class,                                        array("required" => true))
            ->add('memoireDesHommes',   CollectionType::class,                                  array(
                                                                                                            'entry_type'   => UrlType::class,
                                                                                                            'allow_add'  => true,
                                                                                                            'allow_delete' => true,
                                                                                                            "required" => true))
            ->add('militaryUnit',       \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => false, 'class' => 'AppBundle:MilitaryUnit'))
            ->add('rank',               TextType::class,                                        array("required" => false))
            ->add('description',        TextareaType::class,                                    array("required" => false))
            ->add('updateComment',      TextType::class,                                        array("required" => true))
            ->add('isOfficialVersion',  TextType::class,                                        array("required" => false))
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
