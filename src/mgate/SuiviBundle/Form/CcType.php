<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\SuiviBundle\Entity\Etude;

use mgate\PersonneBundle\Form;

class CcType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	   
            $builder->add('acompte','checkbox',array('label'=>'Acompte'))
                    ->add('pourcentageAcompte','integer',array('label'=>'Pourcentage acompte'))
                    ->add('cc',new SuiviCcType(),array('label'=>'Suivi du document'));
            
            
             
            
            /*             ->add('prospect', 'collection', array('type'  => new \mgate\PersonneBundle\Form\ProspectType,
                                              'prototype' => true,
                                              'allow_add' => true)); */
            
    }

    public function getName()
    {
        return 'alex_suivibundle_cctype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        );
    }
}


