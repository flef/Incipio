<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form\Type\SexeType as SexeType;


class EmployeType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
                ->add('prenom')
                ->add('nom')
                ->add('poste', 'text', array('required'=>false))
                ->add('sexe', new SexeType())
                ->add('email', 'email', array('required'=>false))
                ->add('mobile', 'text', array('required'=>false))
                ->add('fix', 'text', array('required'=>false))
                ->add('adresse', 'textarea', array('required'=>false));
            
    }

    public function getName()
    {
        return 'mgate_personnebundle_employetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\PersonneBundle\Entity\Employe',
        );
    }
}

