<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\Prospect as Prospect;
use mgate\PersonneBundle\Entity\User as User;

use mgate\SuiviBundle\Form\Type\PrestationType as PrestationType;

class EtudeType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            ->add('prospect', 'entity', 
                array ('label' => 'Client',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Prospect',
                       'property' => 'nom',
                       'property_path' => true,
                       'required' => true))
            ->add('nom', 'text')
            //->add('dateCreation',  'date')
            ->add('description','textarea',array('label'=>'Présentation du projet'))
            ->add('descriptionPrestation','textarea',array('label'=>'Description de la prestation proposée par M-GaTE'))
            ->add('typePrestation',new PrestationType())
            ->add('mandat', 'integer', array('data' => '5') )
            ->add('num', 'integer' )
            ->add('suiveur', 'entity', 
                array ('label' => 'Suiveur de projet',
                       'class' => 'mgate\\PersonneBundle\\Entity\\User',
                       'property' => 'username',
                       'property_path' => true,
                       'required' => false))
            ->add('acompte','checkbox',array('label'=>'Acompte'))
            ->add('pourcentageAcompte','integer',array('label'=>'Pourcentage acompte'))
            ->add('fraisDossier','integer',array('label'=>'Frais de dossier'));
            
    }

    public function getName()
    {
        return 'mgate_suivibundle_etudetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        );
    }
}

