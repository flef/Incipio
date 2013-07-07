<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MandatType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
                ->add('debutMandat', 'genemu_jquerydate', array('label'=>'Date de début', 'format'=>'d/MM/y', 'required'=>false, 'widget'=>'single_text'))
                ->add('finMandat', 'genemu_jquerydate', array('label'=>'Date de Fin', 'format'=>'d/MM/y', 'required'=>false, 'widget'=>'single_text'))
                ->add('poste', 'entity', 
                    array ('label' => 'Intitulé',
                           'class' => 'mgate\\PersonneBundle\\Entity\\Poste',
                           'property' => 'intitule',
                           'property_path' => true,
                           'required' => false,));
                    
          
    }

    public function getName()
    {
        return 'mgate_personnebundle_mandatetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\PersonneBundle\Entity\Mandat',
        );
    }
}

