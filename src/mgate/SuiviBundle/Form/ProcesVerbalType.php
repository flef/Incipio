<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Form\ProcesVerbalSubType;


class ProcesVerbalType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add(strtolower($options['type']), new ProcesVerbalSubType(), array('label' => ' ', 'type'=>$options['type'], 'prospect'=>$options['prospect'], 'phases'=>$options['phases']));
                 
    }

    public function getName() {
        return 'mgate_suivibundle_ProcesVerbaltype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
            'type' => '',
            'prospect' => '',
            'phases' => '',
        );
    }

}
