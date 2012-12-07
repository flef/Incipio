<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;

class ApType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    DocTypeType::buildForm($builder,$options);
            $builder->add('fraisDossier','integer');
             
            
            
            
    }

    public function getName()
    {
        return 'alex_suivibundle_aptype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Ap',
        );
    }
}


