<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;

class MissionType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            //->add('dateCreation',  'date')
            ->add('intervenant','entity', 
                array ('label' => 'Intervenant',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'nom',
                       'property_path' => true,
                       'required' => false))
            ->add('debutOm','genemu_jquerydate', array('label'=>'Début du Récapitulatif de Mission','required'=>false, 'widget'=>'single_text'))
            ->add('finOm','genemu_jquerydate', array('label'=>'Fin du Récapitulatif de Mission','required'=>false, 'widget'=>'single_text'))
            ->add('nbjeh','integer',array('label'=>'Nombre de JEH'))
            ->add('avancement','integer',array('label'=>'Avancement en %'))
            ->add('rapportDemande','checkbox', array('label'=>'Rapport pédagogique demandé','required'=>false))
            ->add('rapportRelu','checkbox', array('label'=>'Rapport pédagogique relu','required'=>false))
            ->add('remunere','checkbox', array('label'=>'Intervenant rémunéré','required'=>false));
            DocTypeType::buildForm($builder,$options);
            
             
            
            /*             ->add('prospect', 'collection', array('type'  => new \mgate\PersonneBundle\Form\ProspectType,
                                              'prototype' => true,
                                              'allow_add' => true)); */
            
    }

    public function getName()
    {
        return 'alex_suivibundle_mssiontype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Mission',
        );
    }
}


