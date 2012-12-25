<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Entity\Ap;
use mgate\SuiviBundle\Entity\Etude;

class ApType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	   // DocTypeType::buildForm($builder, $options);
            $builder->add('fraisDossier','integer',array('label'=>'Frais de dossier'))
                    ->add('description','textarea',array('label'=>'Présentation du projet'))
                    ->add('competences','textarea',array('label'=>'Capacité des intervenants:'))
                    ->add('ap',new SuiviApType(),array('label'=>'Suivi du document'));
  
    }

    public function getName()
    {
        return 'alex_suivibundle_aptype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        );
    }
}


