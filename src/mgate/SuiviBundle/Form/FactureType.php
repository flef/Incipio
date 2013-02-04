<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Form\FactureSubType;


class FactureType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add(strtolower($options['type']), new FactureSubType(), array('label' => ' ', 'type'=>$options['type']));
        
        if(strtolower($options['type'])=="fa")
                $builder->add('pourcentageAcompte', 'integer', array('label' => 'Pourcentage de l\'Acompte', 'required' => false, 'attr' => array('class' => 'pourcentageAcompte')));
        
    }

    public function getName() {
        return 'mgate_suivibundle_facturetype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
            'type' => '',
        );
    }

}
