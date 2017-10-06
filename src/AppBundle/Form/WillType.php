<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
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
            ->add('minuteLink',                 UrlType::class,                                         array("required" => false))
            ->add('title',                      TextType::class,                                        array("required" => false))
            ->add('minuteDate',                 TextType::class,                                        array("required" => true))
            ->add('minuteYear',                 TextType::class,                                        array("required" => true))
            ->add('willWritingDate',            TextType::class,                                        array("required" => true))
            ->add('willWritingYear',            TextType::class,                                        array("required" => true))
            ->add('willWritingPlace',           \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => false, 'class' => 'AppBundle:Place'))
            ->add('entity',                     \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => false, 'class' => 'AppBundle:Entity'))
            ->add('testator',                   \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,   array("required" => true, 'class' => 'AppBundle:Testator'))
            ->add('pagePhysDescSupport',        TextType::class,                                        array("required" => false))
            ->add('pagePhysDescHeight',         TextType::class,                                        array("required" => false))
            ->add('pagePhysDescWidth',          TextType::class,                                        array("required" => false))
            ->add('pagePhysDescHand',           TextType::class,                                        array("required" => false))
            ->add('envelopePhysDescSupport',    TextType::class,                                        array("required" => false))
            ->add('envelopePhysDescHeight',     TextType::class,                                        array("required" => false))
            ->add('envelopePhysDescWidth',      TextType::class,                                        array("required" => false))
            ->add('envelopePhysDescHand',       TextType::class,                                        array("required" => false))
            ->add('hostingOrganization',        TextType::class,                                        array("required" => true))
            ->add('identificationUser',         TextType::class,                                        array("required" => true))
            ->add('description',                TextareaType::class,                                    array("required" => false))
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
