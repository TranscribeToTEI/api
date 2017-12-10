<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
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
            ->add('callNumber',                 TextType::class,                                        array("required" => true))
            ->add('notaryNumber',               TextType::class,                                        array("required" => true))
            ->add('crpcenNumber',               TextType::class,                                        array("required" => false))
            ->add('minuteLink',                 UrlType::class,                                         array("required" => false))
            ->add('title',                      TextType::class,                                        array("required" => false))
            ->add('minuteDateString',           TextareaType::class,                                    array("required" => true))
            ->add('minuteDateNormalized',       DateType::class,                                        array("required" => true, 'widget' => 'single_text'))
            ->add('minuteDateEndNormalized',    DateType::class,                                        array("required" => false, 'widget' => 'single_text'))
            ->add('minuteYear',                 TextType::class,                                        array("required" => true))
            ->add('willWritingDateString',      TextareaType::class,                                    array("required" => true))
            ->add('willWritingDateNormalized',  DateType::class,                                        array("required" => true, 'widget' => 'single_text'))
            ->add('willWritingDateEndNormalized',DateType::class,                                       array("required" => false, 'widget' => 'single_text'))
            ->add('willWritingYear',            TextType::class,                                        array("required" => true))
            ->add('willWritingPlaceNormalized', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => false, 'class' => 'AppBundle:Place'))
            ->add('willWritingPlaceString',     TextareaType::class,                                    array("required" => false))
            ->add('entity',                     \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => false, 'class' => 'AppBundle:Entity'))
            ->add('testator',                   \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:Testator'))
            ->add('pagePhysDescSupport',        TextType::class,                                        array("required" => false))
            ->add('pagePhysDescHeight',         TextType::class,                                        array("required" => false))
            ->add('pagePhysDescWidth',          TextType::class,                                        array("required" => false))
            ->add('pagePhysDescHand',           TextType::class,                                        array("required" => false))
            ->add('pagePhysDescNumber',         TextType::class,                                        array("required" => false))
            ->add('envelopePhysDescSupport',    TextType::class,                                        array("required" => false))
            ->add('envelopePhysDescHeight',     TextType::class,                                        array("required" => false))
            ->add('envelopePhysDescWidth',      TextType::class,                                        array("required" => false))
            ->add('envelopePhysDescHand',       TextType::class,                                        array("required" => false))
            ->add('codicilPhysDescSupport',     TextType::class,                                        array("required" => false))
            ->add('codicilPhysDescHeight',      TextType::class,                                        array("required" => false))
            ->add('codicilPhysDescWidth',       TextType::class,                                        array("required" => false))
            ->add('codicilPhysDescHand',        TextType::class,                                        array("required" => false))
            ->add('codicilPhysDescNumber',      TextType::class,                                        array("required" => false))
            ->add('willType',                   \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:WillType'))
            ->add('hostingOrganization',        \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:HostingOrganization'))
            ->add('identificationUsers',        TextareaType::class,                                    array("required" => true))
            ->add('description',                TextareaType::class,                                    array("required" => false))
            ->add('additionalComments',         TextareaType::class,                                    array("required" => false))
            ->add('isOfficialVersion',          TextType::class,                                        array("required" => false))
            ->add('updateComment',              TextType::class,                                        array("required" => false))
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
