<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;

class DocTypeType extends AbstractType
{
    private $type;
    
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
            ->add('signataire2', 'entity', 
                array ('label' => 'Signataire Client',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'query_builder' => function(PersonneRepository $pr) { return $pr->getEmployeOnly(); },
                       'required' => false))
            ->add('dateSignature', 'genemu_jquerydate', array('label'=>'Date de Signature du document','required'=>false, 'widget'=>'single_text'));
            
    }
    
    public function __construct($type = null)
    {
        $this->type = $type;
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


