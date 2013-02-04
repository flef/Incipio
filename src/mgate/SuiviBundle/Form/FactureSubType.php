<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;

class FactureSubType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        DocTypeType::buildForm($builder, $options);
	//$builder->add('type');
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


