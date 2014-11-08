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
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;
use mgate\PersonneBundle\Form\MembreType as MembreType;

class MissionType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('knownIntervenant', 'checkbox', array(
                'required' => false,
                'label' => "L'intervenant existe-t-il déjà dans la base de donnée ?"
                ))
            ->add('intervenant', 'genemu_jqueryselect2_entity', array(
               'class' => 'mgate\\PersonneBundle\\Entity\\Membre',
               'property' => 'personne.prenomNom',
               'label' => 'Intervenant',
               //'query_builder' => function(PersonneRepository $pr) { return $pr->getMembreOnly(); },
               'required' => false
               ))
           ->add('newIntervenant', new MembreType(), array('label' => 'Nouvel intervenant ', 'required' => false))

        
            ->add('debutOm','genemu_jquerydate', array('label'=>'Début du Récapitulatif de Mission','required'=>false, 'widget'=>'single_text'))
            ->add('finOm','genemu_jquerydate', array('label'=>'Fin du Récapitulatif de Mission','required'=>false, 'widget'=>'single_text'))
            ->add('pourcentageJunior', 'percent', array('label'=>'Pourcentage junior', 'required' => false, 'precision' => 2))
            ->add('referentTechnique', 'genemu_jqueryselect2_entity', array(
               'class' => 'mgate\\PersonneBundle\\Entity\\Membre',
               'property' => 'personne.prenomNom',
               'label' => 'Référent Technique',
               'required' => false
               ))
            ->add('repartitionsJEH', 'collection', array(
                'type' => new RepartitionJEHType(),
				'options' => array(
                    'data_class' => 'mgate\SuiviBundle\Entity\RepartitionJEH'
				),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                ));

            //->add('avancement','integer',array('label'=>'Avancement en %'))
            //->add('rapportDemande','checkbox', array('label'=>'Rapport pédagogique demandé','required'=>false))
            //->add('rapportRelu','checkbox', array('label'=>'Rapport pédagogique relu','required'=>false))
            //->add('remunere','checkbox', array('label'=>'Intervenant rémunéré','required'=>false));                    
                               
        //->add('mission', new DocTypeType('mission'), array('label'=>' '));
        DocTypeType::buildForm($builder, $options);
            
            
    }

    public function getName()
    {
        return 'mgate_suivibundle_mssiontype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Mission',
        ));
    }
}
