<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form\Type\SexeType as SexeType;


class PersonneType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
                ->add('prenom')
                ->add('nom')
                ->add('poste')
                ->add('sexe', new SexeType())
                ->add('mobile')
                ->add('fix')
                ->add('adresse');
            
    }

    public function getName()
    {
        return 'mgate_personnebundle_personnetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\PersonneBundle\Entity\Personne',
        );
    }
}

