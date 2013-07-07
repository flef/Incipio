<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form\Type\SexeType as SexeType;


class MembreType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
                ->add('personne', new PersonneType(), array('label'=>' ', 'user'=>true))
                ->add('identifiant', 'text', array('required'=>false,'read_only'=>true))
                ->add('mandats', 'collection', array(
                'type' => new MandatType,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false, //indispensable cf doc
                ));
            
    }

    public function getName()
    {
        return 'mgate_personnebundle_membretype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\PersonneBundle\Entity\Membre',
        );
    }
}

