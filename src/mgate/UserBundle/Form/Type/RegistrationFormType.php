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
        
    }

    public function getName()
    {
        return 'mgate_user_registration';
    }
    

    
}
