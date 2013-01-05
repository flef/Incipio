<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;
use mgate\PersonneBundle\Form\PersonneType as PersonneType;

class DocTypeType extends AbstractType
{
    private $type;
    private $prospect;
    
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
                       'required' => false))

            ->add('knownSignataire2', 'checkbox', array(
                'required' => false,
                'label' => "Le signataire client existe-t-il déjà dans la base de donnée ?"
                ))
             ->add('signataire2', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                'property' => 'prenomNom',
                'label' => 'Signataire client existant',
                'query_builder' => function(PersonneRepository $pr) { return $pr->getEmployeOnly($this->prospect); },
                ))
            ->add('newSignataire2', new PersonneType(), array('label' => 'Nouveau signataire client:', 'required' => false))                               
                      
                               
            ->add('dateSignature', 'genemu_jquerydate', array('label'=>'Date de Signature du document', 'required'=>false, 'widget'=>'single_text'));
            
    }
    
    public function __construct($type = null, $prospect = null)
    {
        $this->type = $type;
        $this->prospect = $prospect;
    }

    public function getName()
    {
        return 'alex_suivibundle_doctypetype';
    }

    public function getDefaultOptions(array $options)
    {
        if($this->type==null)
            return array(
                'data_class' => 'mgate\SuiviBundle\Entity\DocType',
                /*'cascade_validation' => true,*/
            );
        else        
            return array(
                'data_class' => 'mgate\SuiviBundle\Entity\\'.$this->type,
                /*'cascade_validation' => true,*/
            );
    }
}


