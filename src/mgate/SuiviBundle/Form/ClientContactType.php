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


namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\CommentBundle\Form\ThreadType;

use mgate\SuiviBundle\Form\Type\MoyenContactType as MoyenContactType;


class ClientContactType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            //->add('dateCreation',  'date')
            
            ->add('faitPar','genemu_jqueryselect2_entity',array ('label' => 'Fait par',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'required' => true))
            
            //->add('thread', new ThreadType) // délicat 
           ->add('date','datetime',array('label'=>'Date du contact'))
           //->add('date', 'genemu_jquerydate', array('label'=>'Date du contact', 'required'=>true, 'widget'=>'single_text'))
           ->add('objet','text',array('label'=>'Objet'))
           ->add('contenu','textarea',array('label'=>'Résumé du contact', 'attr'=>array('cols'=>'100%','rows'=>5)))
           ->add('moyenContact', new MoyenContactType(), array('label'=>'Contact effectué par'))
           ;
            
             
            
            /*             ->add('prospect', 'collection', array('type'  => new \mgate\PersonneBundle\Form\ProspectType,
                                              'prototype' => true,
                                              'allow_add' => true)); */
            
    }

    public function getName()
    {
        return 'mgate_suivibundle_clientcontacttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
            $resolver->setDefaults(array(
                'data_class' => 'mgate\SuiviBundle\Entity\ClientContact',
            ));
    }
}


