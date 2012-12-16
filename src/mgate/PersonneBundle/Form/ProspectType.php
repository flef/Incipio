<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use mgate\CommentBundle\Form\ThreadType;

class ProspectType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            //->add('thread', new ThreadType) // délicat 
            ->add('nom')
            ->add('entite')
            ->add('adresse')
            ->add('signataire_titre', 'choice', array(
                'choices' => array(
                    'M.' => 'Madame',
                    'Mme.' => 'Monsieur'
                ),
                'required'    => false,
                'empty_value' => 'Choisir le titre',
                'empty_data'  => null))
            ->add('signataire_fonction')
            ->add('signataire_nom')
            ->add('signataire_prenom');
            
    }

    public function getName()
    {
        return 'alex_suivibundle_etudetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\PersonneBundle\Entity\Prospect',
        );
    }
}

