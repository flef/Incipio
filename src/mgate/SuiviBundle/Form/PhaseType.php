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
            $builder->add('position', 'hidden', array('attr' => array('class' => 'position')))
                    ->add('titre', 'text')
                    ->add('objectif', 'textarea', array( 'label' => 'Objectif'))
                    ->add('methodo', 'textarea', array( 'label' => 'Méthodologie'))
                    ->add('validation', 'choice', array('choices' => Phase::getValidationChoice()))
                    ->add('nbrJEH', 'integer', array( 'label' => 'Nombre de JEH'))
                    ->add('prixJEH', 'integer', array( 'label' => 'Prix du JEH HT', 'data' => '300'))
                    ->add('dateDebut', 'jquery_date', array( 'label' => 'Date de début', 'format' => 'd/MM/y'))
                    ->add('delai', 'integer', array( 'label' => 'Durée en nombre de jours'));
  
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


