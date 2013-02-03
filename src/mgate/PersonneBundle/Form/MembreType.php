<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form\Type\SexeType as SexeType;


class MembreType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
                ->add('personne', new PersonneType(), array('label'=>' '))
                ->add('identifiant', 'text', array('required'=>false))
                ->add('poste', 'entity', 
                    array ('label' => 'Séléctionner un poste',
                           'class' => 'mgate\\PersonneBundle\\Entity\\Poste',
                           'property' => 'intitule',
                           'property_path' => true,
                           'required' => false,));

            
    }

    public function getName()
    {
        return 'mgate_personnebundle_membretype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\PersonneBundle\Entity\Membre',
        );
    }
}

