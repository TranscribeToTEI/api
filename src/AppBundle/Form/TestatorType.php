<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('name',                       TextType::class,                                        array("required" => true))
            ->add('indexName',                  TextType::class,                                        array("required" => false))
            ->add('surname',                    TextType::class,                                        array("required" => true))
            ->add('firstnames',                 TextareaType::class,                                    array("required" => true))
            ->add('otherNames',                 TextareaType::class,                                    array("required" => false))
            ->add('profession',                 TextareaType::class,                                    array("required" => false))
            ->add('addressNumber',              TextType::class,                                        array("required" => false))
            ->add('addressStreet',              TextType::class,                                        array("required" => false))
            ->add('addressDistrict',            TextType::class,                                        array("required" => false))
            ->add('addressCity',                \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => false, 'class' => 'AppBundle:Place'))
            ->add('address',                    TextareaType::class,                                    array("required" => false))
            ->add('dateOfBirthString',          TextareaType::class,                                    array("required" => true))
            ->add('dateOfBirthNormalized',      DateType::class,                                        array("required" => true))
            ->add('dateOfBirthEndNormalized',   DateType::class,                                        array("required" => false))
            ->add('yearOfBirth',                TextType::class,                                        array("required" => true))
            ->add('placeOfBirthNormalized',     \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:Place'))
            ->add('placeOfBirthString',         TextareaType::class,                                    array("required" => true))
            ->add('dateOfDeathString',          TextareaType::class,                                    array("required" => true))
            ->add('dateOfDeathNormalized',      DateType::class,                                        array("required" => true))
            ->add('dateOfDeathEndNormalized',   DateType::class,                                        array("required" => false))
            ->add('yearOfDeath',                TextType::class,                                        array("required" => true))
            ->add('placeOfDeathNormalized',     \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:Place'))
            ->add('placeOfDeathString',         TextareaType::class,                                    array("required" => true))
            ->add('deathMention',               TextType::class,                                        array("required" => true))
            ->add('memoireDesHommes',           CollectionType::class,                                  array(
                                                                                                                        'entry_type'   => TextType::class,
                                                                                                                        'allow_add'  => true,
                                                                                                                        'allow_delete' => true,
                                                                                                                        "required" => true))
            ->add('militaryUnitNormalized',     \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => false, 'class' => 'AppBundle:MilitaryUnit'))
            ->add('militaryUnitString',         TextareaType::class,                                    array("required" => false))
            ->add('rank',                       TextType::class,                                        array("required" => false))
            ->add('description',                TextareaType::class,                                    array("required" => false))
            ->add('isOfficialVersion',          CheckboxType::class,                                    array("required" => false))
            ->add('updateComment',              TextType::class,                                        array("required" => true))
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
