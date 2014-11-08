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
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Entity\PersonneRepository;
use mgate\PersonneBundle\Entity\Prospect;
use mgate\PersonneBundle\Form\ProspectType;
use mgate\SuiviBundle\Entity\Etude;

class EtudeType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('knownProspect', 'checkbox', array(
                'required' => false,
                'label' => "Le signataire client existe-t-il déjà dans la base de donnée ?"
                ))
             ->add('prospect', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\PersonneBundle\Entity\Prospect',
                'property' => 'nom',
                'required' => true,
                'label' => 'Prospect existant',
                ))
            ->add('newProspect', new ProspectType(), array('label' => 'Nouveau prospect:', 'required' => false))
            ->add('nom', 'text',array('label'=>'Nom interne de l\'étude'))
            ->add('description','textarea',array('label'=>'Présentation interne de l\'étude', 'required' => false, 'attr'=>array('cols'=>'100%','rows'=>5),))
            ->add('mandat', 'integer' )
            ->add('num', 'integer', array('label'=>'Numéro de l\'étude', 'required' => false))
            ->add('confidentiel', 'checkbox', array('label' => 'Confidentialité :', 'required' => false, 'attr'=>array("title"=>"Si l'étude est confidentielle, elle ne sera visible que par vous et les membres du CA.")))
            ->add('suiveur', 'genemu_jqueryselect2_entity', 
                array ('label' => 'Suiveur de projet',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'query_builder' => function(PersonneRepository $pr) { return $pr->getMembreOnly(); },
                       'required' => false))
            ->add('domaineCompetence', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\SuiviBundle\Entity\DomaineCompetence',
                'property' => 'nom',
                'required' => false,
                'label' => 'Domaine de compétence',
                ))
            ->add('sourceDeProspection', 'choice', array('choices' => Etude::getSourceDeProspectionChoice(), 'required' => false));        
    }

    public function getName()
    {
        return 'mgate_suivibundle_etudetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        ));
    }
}

