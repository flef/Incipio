<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\Prospect as Prospect;
use mgate\PersonneBundle\Entity\User as User;

class EtudeType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            ->add('prospect', 'entity', 
                array ('label' => 'Client',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Prospect',
                       'property' => 'nom',
                       'property_path' => true,
                       'required' => true))
            ->add('nom', 'text')
            //->add('dateCreation',  'date')
            ->add('description')
            ->add('mandat', 'integer', array('data' => '5') )
            ->add('num', 'integer' )
            ->add('suiveur', 'entity', 
                array ('label' => 'Suiveur de projet',
                       'class' => 'mgate\\PersonneBundle\\Entity\\User',
                       'property' => 'username',
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

