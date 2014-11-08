<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


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
