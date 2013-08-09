<?php

namespace mgate\CommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ThreadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('permalink')
            //->add('isCommentable')
            //->add('numComments')
            //->add('lastCommentAt')
            ->add('id')
            //->add('auteur')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\CommentBundle\Entity\Thread'
        ));
    }

    public function getName()
    {
        return 'mgate_commentbundle_threadtype';
    }
}
