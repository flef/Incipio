<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form\Type\SexeType as SexeType;


class PosteType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
                ->add('intitule', 'text', array('required'=>true));
          
    }

    public function getName()
    {
        return 'mgate_personnebundle_posteetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\PersonneBundle\Entity\Poste',
        );
    }
}

