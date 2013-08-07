<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\PersonneRepository;
use mgate\PersonneBundle\Form\PersonneType;
use mgate\PersonneBundle\Form\EmployeType;
use mgate\PersonneBundle\Entity\Prospect;
use mgate\PersonneBundle\Entity\Personne;

class DocTypeType extends AbstractType
{
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        // Version du document
        $builder->add('version', 'integer', array('label'=>'Version du document'));
        
        // Si le document n'est pas une facture
        if($options['data_class']!='mgate\SuiviBundle\Entity\Facture')
        {
             $builder->add('signataire1', 'genemu_jqueryselect2_entity', 
                array ('label' => 'Signataire M-GaTE',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'query_builder' => function(PersonneRepository $pr) { return $pr->getMembresByPoste('president%'); },
                       'required' => true));
        }
        else
        {
             $builder->add('signataire1', 'genemu_jqueryselect2_entity', 
                array ('label' => 'Signataire M-GaTE',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'query_builder' => function(PersonneRepository $pr) { return $pr->getMembresByPoste('tresorier'); },
                       'required' => true));
        }
        
        
        // Si le document n'est ni une facture ni un RM
        if($options['data_class']!='mgate\SuiviBundle\Entity\Mission' // le signataire2 c'est l'intervenant
           && $options['data_class']!='mgate\SuiviBundle\Entity\Facture' // pas de signataire2
           )
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
            ->add('newSignataire2', new EmployeType(), array('label' => 'Nouveau signataire '.$pro->getNom(), 'required' => false, 'signataire' => true, 'mini' => true) );                               
        }

                               
            $builder->add('dateSignature', 'genemu_jquerydate', array('label'=>'Date de Signature du document', 'required'=>false,'format' => 'dd/MM/yyyy', 'widget'=>'single_text'));
            
    }
    
    public function getName()
    {
        return 'mgate_suivibundle_doctypetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\DocType',
            'prospect' => null,
        );
    }
}


