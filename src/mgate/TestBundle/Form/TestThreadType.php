<?php

namespace mgate\TestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestThreadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('permalink')
            //->add('isCommentable')
            //->add('numComments')
            //->add('lastCommentAt')
            ->add('id')
            ->add('auteur')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TestBundle\Entity\TestThread'
        ));
    }

    public function getName()
    {
        return 'mgate_testbundle_testthreadtype';
    }
}
