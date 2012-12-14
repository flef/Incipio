<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Entity\Phase;

class PhaseType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
            $builder->add('validation', 'choice', array('choices' => Phase::getValidationChoice()))
                    ->add('titre')
                    ->add('position', 'hidden', array('attr' => array('class' => 'position')));
  
    }

    public function getName()
    {
        return 'mgate_suivibundle_phasetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Phase',
        );
    }
}


