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
use mgate\SuiviBundle\Entity\Av;

class AvType extends DocTypeType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('differentielDelai', 'integer', array('label' => 'Modification du Délai (+/- x jours)', 'required' => false, ))
        ->add('objet', 'textarea',
        array('label' => 'Exposer les causes de l’Avenant. Ne pas hésiter à détailler l\'historique des relations avec le client et du travail sur l\'étude qui ont conduit à l\'Avenant.',
        'required' => false, ))
        ->add('clauses', 'choice', array('label' => 'Type d\'avenant', 'multiple' => true, 'choices' => Av::getClausesChoices()))
        ->add('phases', 'collection', array(
                'type' => new PhaseType,
                'options' => array('isAvenant' => true),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                ));
                /*->add('avenantsMissions', 'collection', array(
            'type' => new AvMissionType,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false,
        ))*/;

        DocTypeType::buildForm($builder, $options);
    }

    public function getName() {
        return 'mgate_suivibundle_avtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Av',
            'prospect' => '',
        ));
    }

}

