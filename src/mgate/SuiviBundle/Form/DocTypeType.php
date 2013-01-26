<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;
use mgate\PersonneBundle\Form\PersonneType as PersonneType;
use mgate\PersonneBundle\Entity\Prospect as Prospect;

class DocTypeType extends AbstractType
{
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('version', 'integer', array('label'=>'Version du document'))
            ->add('signataire1', 'entity', 
                array ('label' => 'Signataire M-GaTE',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'query_builder' => function(PersonneRepository $pr) { return $pr->getMembreOnly(); },
                       'required' => false));

        if($options['data_class']!='mgate\SuiviBundle\Entity\Mission' && $options['data_class']!='mgate\SuiviBundle\Entity\Facture')
        {
            $pro=$options['prospect'];
            $builder->add('knownSignataire2', 'checkbox', array(
                'required' => false,
                'label' => "Le signataire client existe-t-il déjà dans la base de donnée ?"
                ))
             ->add('signataire2', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                'property' => 'prenomNom',
                'label' => 'Signataire '.$pro->getNom(),
                'query_builder' => function(PersonneRepository $pr) use ($pro) { return $pr->getEmployeOnly($pro); },
                'required' => false
                ))
            ->add('newSignataire2', new PersonneType(), array('label' => 'Nouveau signataire '.$pro->getNom(), 'required' => false, 'mini' => true) );                               
        }
                               
            $builder->add('dateSignature', 'genemu_jquerydate', array('label'=>'Date de Signature du document', 'required'=>false, 'widget'=>'single_text'));
            
    }
    
    public function getName()
    {
        return 'alex_suivibundle_doctypetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\DocType',
            'prospect' => null,
        );
    }
}


