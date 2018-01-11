<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppPreferenceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projectTitle',               TextType::class,        array("required" => true))
            ->add('helpHomeContent',            TextType::class,        array("required" => false))
            ->add('helpInsideHomeContent',      TextType::class,        array("required" => false))
            ->add('discoverHomeContent',        TextType::class,        array("required" => false))
            ->add('aboutContent',               TextType::class,        array("required" => false))
            ->add('legalNoticesContent',        TextType::class,        array("required" => false))
            ->add('creditsContent',             TextType::class,        array("required" => false))
            ->add('facebookPageId',             TextType::class,        array("required" => false))
            ->add('twitterId',                  TextType::class,        array("required" => false))
            ->add('enableContact',              CheckboxType::class,    array("required" => false))
            ->add('systemEmail',                EmailType::class,       array("required" => true))
            ->add('contactEmail',               EmailType::class,       array("required" => false))
            ->add('enableRegister',             CheckboxType::class,    array("required" => false))
            ->add('transcriptEditAccess',       CheckboxType::class,    array("required" => false))
            ->add('taxonomyEditAccess',         TextType::class,        array("required" => false, "description" => "Allowed values: \"selfAuthorization\", \"controlledAuthorization\", \"free\", \"forbidden\""))
            ->add('taxonomyAskQuestion',        TextareaType::class,    array("required" => false))
            ->add('taxonomyAccessProposal',     TextareaType::class,    array("required" => false))
            ->add('trainingHomeContent',        TextareaType::class,    array("required" => false))
            ->add('infoContentEditTaxonomy',    TextareaType::class,    array("required" => false))
            ->add('infoContact',                TextareaType::class,    array("required" => false))
            ->add('updateComment',              TextareaType::class,    array("required" => false))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AppPreference',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_apppreference';
    }


}
