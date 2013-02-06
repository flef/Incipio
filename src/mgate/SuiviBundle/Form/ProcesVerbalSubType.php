<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;

class ProcesVerbalSubType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        
        /*if($options['type']=="pvr")
            $readOnly=true;*/
        
        DocTypeType::buildForm($builder, $options);
    }

    public function getName() {
        return 'mgate_suivibundle_procesverbalsubtype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\ProcesVerbal',
            'type' => null,
            'prospect' => null,
        );
    }
}


