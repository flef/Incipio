<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Entity\Ap;

class ApType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    DocTypeType::buildForm($builder, $options);
            $builder->add('fraisDossier','integer')
                    ->add('presentationprojet','textarea',array('label'=>'Présentation du projet'))
                    ->add('typePrestation', 'choice', array('choices'=>Ap::getValidationChoice(),'required'=>false,'label'=>'Type de prestation'))
                    ->add('descriptionprestation','textarea',array('label'=>'Description de la prestation'))
                    ->add('capacitedev','textarea',array('label'=>'Capacité des intervenants:'));
  
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


