<?php

namespace mgate\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

use mgate\PersonneBundle\Form\PersonneType as PersonneType;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;
use mgate\UserBundle\Entity\User as User;

class UserAdminType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder->add('personne', 'entity', 
                array ('label' => 'Séléctionner la personne',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'property_path' => true,
                       'required' => false,
                       //'query_builder' => function(PersonneRepository $pr) { return $pr->getNotUser(); },
                               
                               ));   */
                       
       $builder->add('enabled', 'checkbox', array(
            'label'     => 'Adresse email validé ?',
            'required'  => false,
       ));   
       $builder->add('roles', 'choice', array(
        'choices' => User::getRolesNames(),
        'required' => false,'label'=>'Roles','multiple'=>true
        ));   
                       
        parent::buildForm($builder, $options);
        
    }

    public function getName()
    {
        return 'mgate_user_useradmin';
    }
    

    
}
