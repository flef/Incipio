<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;

class FactureSubType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        
        if($options['type']=="fs" || $options['type']=="fa")
            $readOnly=true;
        else
            $readOnly=false;
        
        $builder->add('montantHT', 'money', array( 'label'=>'Montant HT', 'required'=>true, 'read_only'=>$readOnly, 'attr' => array('class' => 'montantHT')));
        $builder->add('num', 'integer', array( 'label'=>'Numéro Facture Comptable', 'required'=>true, 'read_only'=>false,));
        
        DocTypeType::buildForm($builder, $options);
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


