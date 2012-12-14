<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\Prospect as Prospect;

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
            ->add('nom')
            //->add('dateCreation',  'date')
            ->add('description')
            ->add('mandat', 'integer', array('data' => '5') );
            
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

