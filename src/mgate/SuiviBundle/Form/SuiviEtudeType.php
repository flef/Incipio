<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;
use mgate\SuiviBundle\Entity\Etude;

class SuiviEtudeType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {


        $builder->add('stateID', 'choice', array('choices' => Etude::getStateIDChoice(), 'label' => 'Etat de l\'Étude', 'required' => true))
                ->add('auditDate', 'genemu_jquerydate', array('label' => 'Audité le', 'format' => 'd/MM/y', 'required' => false, 'widget' => 'single_text'))
                ->add('auditType', 'choice', array('choices' => Etude::getAuditTypeChoice(), 'label' => 'Type d\'audit', 'required' => false))
                ->add('stateDescription','textarea',array('label'=>'Problèmes', 'required' => false, 'attr'=>array('cols'=>'100%','rows'=>5),))
                ->add('ap', new DocTypeSuiviType(), array('label' => 'Avant-Projet', 'data_class' => 'mgate\SuiviBundle\Entity\Ap'))
                ->add('cc', new DocTypeSuiviType(), array('label' => 'Convention Client', 'data_class' => 'mgate\SuiviBundle\Entity\Cc'));

        $builder->add('FactureVentes', 'collection', array(
            'type' => new DocTypeSuiviType,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false, //indispensable cf doc
        ));

        $builder->add('missions', 'collection', array(
            'type' => new DocTypeSuiviType,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false, //indispensable cf doc
        ));

        $builder->add('pvis', 'collection', array(
            'type' => new DocTypeSuiviType,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false, //indispensable cf doc
        ));

        $builder->add('pvr', new DocTypeSuiviType(), array('label' => 'PVR', 'data_class' => 'mgate\SuiviBundle\Entity\ProcesVerbal'));
    }

    public function getName() {
        return 'mgate_suivibundle_suivietudetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        ));
    }

}

