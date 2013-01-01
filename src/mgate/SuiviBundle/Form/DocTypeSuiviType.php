<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;

class DocTypeSuiviType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            ->add('redige', 'checkbox', array('label'=>'Est-ce que le document est rédigé ?','required'=>false))
            ->add('relu', 'checkbox', array('label'=>'Est-ce que le document est relu ?','required'=>false))
            ->add('spt1', 'checkbox', array('label'=>'Est-ce que le document est signé paraphé tamponné par M-GaTE ?','required'=>false))
            ->add('spt2', 'checkbox', array('label'=>'Est-ce que le document est signé paraphé tamponné par le client ?','required'=>false))
            ->add('envoye', 'checkbox', array('label'=>'Est-ce que le document est envoyé ?','required'=>false))
            ->add('receptionne', 'checkbox', array('label'=>'Est-ce que le document est réceptionné ?','required'=>false));            

    }

    public function getName()
    {
        return 'alex_suivibundle_doctypetype';
    }

    public function getDefaultOptions(array $options)
    {
        if($options=="ap")
            return array('data_class' => 'mgate\SuiviBundle_Entity\Ap');
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\DocType',
            /*'cascade_validation' => true,*/
        );
    }
}


