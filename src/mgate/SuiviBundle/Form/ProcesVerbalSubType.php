<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;

class ProcesVerbalSubType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $phaseNum = $options['phases'];
        if($options['type']=="pvi"){
            $builder->add('phaseID', 'integer', array('label' => 'Phases concernées', 'required' => false, 'attr' => array('min' => '1', 'max' => $phaseNum)));
        }
        
        DocTypeType::buildForm($builder, $options);
    }

    public function getName() {
        return 'mgate_suivibundle_procesverbalsubtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\ProcesVerbal',
            'type' => null,
            'prospect' => null,
            'phases' => null,
        );
    }
}


