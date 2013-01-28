<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class PhaseMissionType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	// Je ne sais pas trop comment faire, Stéphane 
        
        $builder->add('phase', 'collection', array(
                'type' => new PhaseType,
                'allow_add' => false,
                'allow_delete' => false,
                'prototype' => true,
                'by_reference' => false, //indispensable cf doc
                ))
          ->add('nbrJEH', 'integer', array('label'=>'Nombre de JEH', 'required'=>false ));

    }

    public function getName()
    {
        return 'mgate_suivibundle_etudephasestype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        );
    }
}

