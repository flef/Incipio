<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form\Type\SexeType as SexeType;


class EmployeType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
                ->add('personne', new PersonneType(), array('label'=>' ', 'signataire' => $options['signataire'], 'mini' => $options['mini']))
                ->add('poste');
            
    }

    public function getName()
    {
        return 'mgate_personnebundle_employetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\PersonneBundle\Entity\Employe',
            'mini' => false,
            'signataire' => false
        );
    }
}

