<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Entity\Ap;
use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\Type\PrestationType as PrestationType;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;

class ApType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	   // DocTypeType::buildForm($builder, $options);
            $builder->add('suiveur', 'entity', 
                array ('label' => 'Suiveur de projet',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'query_builder' => function(PersonneRepository $pr) { return $pr->getMembreOnly(); },
                       'required' => false))
                    ->add('ap', new DocTypeType('Ap'), array('label'=>' '))
                    ->add('fraisDossier','integer',array('label'=>'Frais de dossier'))
                    ->add('presentationProjet','textarea',array('label'=>'Présentation du projet'))
                    ->add('descriptionPrestation','textarea',array('label'=>'Description de la prestation proposée par M-GaTE'))
                    ->add('typePrestation',new PrestationType(),array('label'=>'Type de prestation'))
                    ->add('competences','textarea',array('label'=>'Capacité des intervenants:'));
  
    }

    public function getName()
    {
        return 'alex_suivibundle_aptype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        );
    }
}


