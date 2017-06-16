<?php

namespace DataBundle\Form;

use Symfony\Component\Form\AbstractType;
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
            ->add('fullName')
            ->add('surname')
            ->add('firstnames')
            ->add('profession')
            ->add('address')
            ->add('dateOfBirth')
            ->add('placeOfBirth')
            ->add('dateOfDeath')
            ->add('placeOfDeath')
            ->add('deathMention')
            ->add('memoireDesHommes')
            ->add('regiment')
            ->add('rank')
            ->add('createUser');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DataBundle\Entity\Testator',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'databundle_testator';
    }


}
