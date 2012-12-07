<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


use mgate\PersonneBundle\Form;

class FactureType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            ->add('type');
            DocTypeType::buildForm($builder,$options);
            
            
            
    }

    public function getName()
    {
        return 'alex_suivibundle_facturetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Facture',
        );
    }
}


