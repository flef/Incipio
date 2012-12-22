<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;

class DocTypeType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            //->add('dateCreation',  'date')
            ->add('version', 'integer', array('label'=>'Version du document'))
            ->add('signataire1', 'entity', 
                array ('label' => 'Signataire M-GaTE',
                       'class' => 'mgate\\PersonneBundle\\Entity\\User',
                       'property' => 'username',
                       'property_path' => true,
                       'required' => true))
            ->add('signataire2', 'entity', 
                array ('label' => 'Signataire Client',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Employe',
                       'property' => 'nom',
                       'property_path' => true,
                       'required' => true))
            ->add('redige', 'checkbox', array('label'=>'Est-ce que le document est rédigé ?','required'=>false))
            ->add('relu', 'checkbox', array('label'=>'Est-ce que le document est relu ?','required'=>false))
            ->add('spt1', 'checkbox', array('label'=>'Est-ce que le document est signé paraphé tamponné par M-GaTE ?','required'=>false))
            ->add('spt2', 'checkbox', array('label'=>'Est-ce que le document est signé paraphé tamponné par le client ?','required'=>false))
            ->add('envoye', 'checkbox', array('label'=>'Est-ce que le document est envoyé ?','required'=>false))
            ->add('receptionne', 'checkbox', array('label'=>'Est-ce que le document est réceptionné ?','required'=>false))
            //->add('montant', 'money', array('label'=>'Montant')) // inutile ?  
            ->add('dateSignature', 'genemu_jquerydate', array('label'=>'Date de Signature du document','required'=>false, 'widget'=>'single_text'));
            
            /*             ->add('prospect', 'collection', array('type'  => new \mgate\PersonneBundle\Form\ProspectType,
                                              'prototype' => true,
                                              'allow_add' => true)); */
            
    }

    public function getName()
    {
        return 'alex_suivibundle_doctypetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\DocType',
            /*'cascade_validation' => true,*/
        );
    }
}


