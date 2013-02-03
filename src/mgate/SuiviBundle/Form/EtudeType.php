<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\Prospect as Prospect;
use mgate\PersonneBundle\Entity\Personne as Personne;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;

use mgate\SuiviBundle\Form\Type\PrestationType as PrestationType;
use mgate\PersonneBundle\Form\ProspectType as ProspectType;

class EtudeType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('knownProspect', 'checkbox', array(
                'required' => false,
                'label' => "Le signataire client existe-t-il déjà dans la base de donnée ?"
                ))
             ->add('prospect', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\PersonneBundle\Entity\Prospect',
                'property' => 'nom',
                'required' => true,
                'label' => 'Prospect existant',
                ))
            ->add('newProspect', new ProspectType(), array('label' => 'Nouveau prospect:', 'required' => false))                               
            ->add('nom', 'text',array('label'=>'Nom interne de l\'étude', 'help' => 'Test de aide', 'attr' => array('placeholder' => 'First Name')))
            ->add('description','textarea',array('label'=>'Présentation interne de l\'étude'))
            ->add('mandat', 'integer' )
            ->add('num', 'integer', array('label'=>'Numéro de l\'étude'))
            ->add('suiveur', 'entity', 
                array ('label' => 'Suiveur de projet',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'query_builder' => function(PersonneRepository $pr) { return $pr->getMembreOnly(); },
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

