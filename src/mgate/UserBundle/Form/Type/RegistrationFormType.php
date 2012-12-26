<?php

namespace mgate\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use mgate\PersonneBundle\Form\PersonneType as PersonneType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('personne', 'entity', 
                array ('label' => 'Séléctionner la personne',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'nom',
                       'property_path' => true,
                       'required' => true));   
        parent::buildForm($builder, $options);
        
        //$builder->add('personne', new PersonneType(), array('label'=>' '));
            //->add('username', null, array('label' => 'Nom dutilisateur', 'translation_domain' => 'FOSUserBundle'));
    }

    public function getName()
    {
        return 'mgate_user_registration';
    }
}
