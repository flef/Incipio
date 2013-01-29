<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class MissionsRepartitionType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder->add('missions', 'collection', array(
                'type' => new MissionRepartitionType,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false, //indispensable cf doc
                ));
    }

    public function getName()
    {
        return 'mgate_suivibundle_missionsrepartitiontype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        );
    }
}

class MissionRepartitionType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pourcentageJunior', 'integer', array('label'=>'Pourcentage junior', 'required' => false))
            // Je ne sais pas trop comment faire, Stéphane
            ->add('phaseMission', 'collection', array(
                    'type' => new PhaseMissionType,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false, //indispensable cf doc
                    ));
            //->add('avancement','integer',array('label'=>'Avancement en %'))
            //->add('rapportDemande','checkbox', array('label'=>'Rapport pédagogique demandé','required'=>false))
            //->add('rapportRelu','checkbox', array('label'=>'Rapport pédagogique relu','required'=>false))
            //->add('remunere','checkbox', array('label'=>'Intervenant rémunéré','required'=>false));                    
                               
        //->add('mission', new DocTypeType('mission'), array('label'=>' '));           
            
    }

    public function getName()
    {
        return 'mgate_suivibundle_missionrepartitiontype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Mission',
        );
    }
}

