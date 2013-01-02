<?php

namespace mgate\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use mgate\PersonneBundle\Form\PersonneType as PersonneType;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('personne', 'entity', 
                array ('label' => 'Séléctionner la personne',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'required' => true,
                       'query_builder' => function(PersonneRepository $pr) { return $pr->getNotUser(); },));   
        parent::buildForm($builder, $options);
        
    }

    public function getName()
    {
        return 'mgate_user_registration';
    }
}
