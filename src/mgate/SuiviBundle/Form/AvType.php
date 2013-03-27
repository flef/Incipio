<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;

class AvType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            //->add('dateCreation',  'date')
            ->add('fraisDossier','integer')
            ->add('type')
            ;
            DocTypeType::buildForm($builder,$options);
             
            
            /*             ->add('prospect', 'collection', array('type'  => new \mgate\PersonneBundle\Form\ProspectType,
                                              'prototype' => true,
                                              'allow_add' => true)); */
            
    }

    public function getName()
    {
        return 'alex_suivibundle_avtype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Av',
            'prospect' => '',
        );
    }
}


