<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;


class FactureType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('fa', new SubFactureType(), array('label' => ' ', 'type'=>$options['type']))
                ->add('pourcentageAcompte', 'percent', array('label' => 'Pourcentage de l\'Acompte', 'required' => false));
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

class SubFactureType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        DocTypeType::buildForm($builder, $options);
	$builder->add('type');
        //$builder->add('montantHT');
    }

    public function getName() {
        return 'mgate_suivibundle_subfacturetype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Facture',
            'type' => null
        );
    }
}


