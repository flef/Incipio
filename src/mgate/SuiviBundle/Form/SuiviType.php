<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\CommentBundle\Form\ThreadType;

class SuiviType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            //->add('dateCreation',  'date')
            ->add('faitPar','entity',array ('label' => 'Fait par',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'required' => true))
            ->add('date','genemu_jquerydate', array('label'=>'Date de l\'évènement', 'widget'=>'single_text'))
            ->add('contenu','textarea', array('label'=>'Que s\'est-il passé ?'));
            //->add('thread', new ThreadType) // délicat ;
            
             
            
            /*             ->add('prospect', 'collection', array('type'  => new \mgate\PersonneBundle\Form\ProspectType,
                                              'prototype' => true,
                                              'allow_add' => true)); */
            
    }

    public function getName()
    {
        return 'alex_suivibundle_mssiontype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Suivi',
        );
    }
}


