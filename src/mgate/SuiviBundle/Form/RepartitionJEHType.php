<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Form\ProcesVerbalSubType;


class RepartitionJEHType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('nbrJEH','integer',array('required'=>true))
                ->add('prixJEH', 'integer', array('required'=>true, 'attr' => array('min' => 80, 'max' => 300)));
    }
    
    public function getName() {
        return 'mgate_suivibundle_RepartitionJEHType';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\RepartitionJEH',
            'type' => '',
            'prospect' => '',
            'phases' => '',
        );
    }

}
