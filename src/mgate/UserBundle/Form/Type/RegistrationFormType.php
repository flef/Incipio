<?php

namespace mgate\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use mgate\PersonneBundle\Form\PersonneType as PersonneType;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;
use mgate\UserBundle\Entity\User as User;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options); 
        
       $builder->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle', 'attr'=>array("title"=>"prenom.nom")))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr'=>array("title"=>"Utilisé seulement pour les notifications My M-GaTE")));   
        
        // changement de stratégie, plus grande indépendance des comptes utilisateurs
        //$builder->add('personne', new PersonneType(), array('label' => ' ', 'user' => true));
        
    }

    public function getName()
    {
        return 'mgate_user_registration';
    }
    

    
}
