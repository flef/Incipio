<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Form\FactureVenteSubType;


class FactureVenteType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add(strtolower($options['type']), new FactureVenteSubType(), array('label' => ' ', 'type'=>$options['type']));
        
        if(strtolower($options['type'])=="fa")
                $builder->add('pourcentageAcompte', 'percent', array('label' => 'Pourcentage de l\'Acompte', 'required' => false, 'attr' => array('class' => 'pourcentageAcompte')));
    }

    public function getName() {
        return 'mgate_suivibundle_FactureVentetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
            'type' => '',
        ));
    }

}
