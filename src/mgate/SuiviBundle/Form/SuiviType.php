<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\SuiviBundle\Entity\Etude;

class SuiviType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        
        $builder->add('ap', new DocTypeSuiviType(), array('label' => 'Avant-Projet', 'data_class'=>'mgate\SuiviBundle\Entity\Ap'));
        $builder->add('cc', new DocTypeSuiviType(), array('label' => 'Convention Client', 'data_class'=>'mgate\SuiviBundle\Entity\Cc'));
        
        $builder->add('missions', 'collection', array(
                'type' => new DocTypeSuiviType,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false, //indispensable cf doc
                ));
    }

    public function getName() {
        return 'mgate_suivibundle_suivitype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        );
    }

}

