<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Entity\Av;

class AvType extends DocTypeType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('differentielDelai', 'integer', array('label' => 'Modification du Délai (+/- x jours)', 'required' => true, ))
        ->add('objet', 'textarea',
        array('label' => 'Exposer les causes de l’Avenant. Ne pas hésiter à détailler l\'historique des relations avec le client et du travail sur l\'étude qui ont conduit à l\'Avenant.',
        'required' => true, ))
        ->add('clauses', 'choice', array('label' => 'Type d\'avenant', 'multiple' => true, 'choices' => Av::getClausesChoices()))
        ->add('phases', 'collection', array(
                'type' => new PhaseType,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                ));
                /*->add('avenantsMissions', 'collection', array(
            'type' => new AvMissionType,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false,
        ))*/;

        DocTypeType::buildForm($builder, $options);
    }

    public function getName() {
        return 'mgate_suivibundle_avtype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Av',
            'prospect' => '',
        );
    }

}

