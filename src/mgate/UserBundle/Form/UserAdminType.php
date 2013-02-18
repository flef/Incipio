<?php

namespace mgate\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

use mgate\PersonneBundle\Form\PersonneType as PersonneType;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;
use mgate\UserBundle\Entity\User as User;
use mgate\UserBundle\Form\EventListener\AddMembreFieldSubscriber;
//use mgate\PersonneBundle\Form\EventListener\AddMembreFieldSubscriber;

class UserAdminType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $subscriber = new AddMembreFieldSubscriber($builder->getFormFactory());
        $builder->addEventSubscriber($subscriber);
        
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
