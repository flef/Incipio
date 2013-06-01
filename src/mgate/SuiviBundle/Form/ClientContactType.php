<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\CommentBundle\Form\ThreadType;

use mgate\SuiviBundle\Form\Type\MoyenContactType as MoyenContactType;

class ClientContactType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            //->add('dateCreation',  'date')
            
            ->add('faitPar','genemu_jqueryselect2_entity',array ('label' => 'Fait par',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'required' => true))
            
            //->add('thread', new ThreadType) // délicat 
           ->add('date','datetime',array('label'=>'Date du contact'))
           //->add('date', 'genemu_jquerydate', array('label'=>'Date du contact', 'required'=>true, 'widget'=>'single_text'))
           ->add('objet','text',array('label'=>'Objet'))
           ->add('contenu','textarea',array('label'=>'Résumé du contact'))
           ->add('moyenContact', new MoyenContactType(), array('label'=>'Contact effectué par'))
           ;
            
             
            
            /*             ->add('prospect', 'collection', array('type'  => new \mgate\PersonneBundle\Form\ProspectType,
                                              'prototype' => true,
                                              'allow_add' => true)); */
            
    }

    public function getName()
    {
        return 'alex_suivibundle_clientcontacttype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\ClientContact',
        );
    }
}


