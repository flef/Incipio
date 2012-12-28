<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\Prospect as Prospect;
use mgate\PersonneBundle\Entity\Personne as Personne;

use mgate\SuiviBundle\Form\Type\PrestationType as PrestationType;

class EtudeType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prospect', 'genemu_jqueryautocompleter_entity',
                array(  'route_name' => 'ajax_prospect',
                        'class' => 'mgate\PersonneBundle\Entity\Prospect',
                        'property' => 'nom',
                    ))
            ->add('nom', 'text',array('label'=>'Nom interne de l\'étude'))
            ->add('description','textarea',array('label'=>'Présentation interne de l\'étude'))
            ->add('mandat', 'integer', array('data' => '5') )
            ->add('num', 'integer', array('label'=>'Numéro de l\'étude'))
            ->add('suiveur', 'entity', 
                array ('label' => 'Suiveur de projet',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'required' => false));            
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

