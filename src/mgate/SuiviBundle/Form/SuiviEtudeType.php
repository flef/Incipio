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
use mgate\SuiviBundle\Entity\Etude;

class SuiviEtudeType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {


        $builder->add('stateID', 'choice', array('choices' => Etude::getStateIDChoice(), 'label' => 'Etat de l\'Étude', 'required' => true))
                ->add('auditDate', 'genemu_jquerydate', array('label' => 'Audité le', 'format' => 'd/MM/y', 'required' => false, 'widget' => 'single_text'))
                ->add('auditType', 'choice', array('choices' => Etude::getAuditTypeChoice(), 'label' => 'Type d\'audit', 'required' => false))
                ->add('stateDescription','textarea',array('label'=>'Problèmes', 'required' => false, 'attr'=>array('cols'=>'100%','rows'=>5),))
                ->add('ap', new DocTypeSuiviType(), array('label' => 'Avant-Projet', 'data_class' => 'mgate\SuiviBundle\Entity\Ap'))
                ->add('cc', new DocTypeSuiviType(), array('label' => 'Convention Client', 'data_class' => 'mgate\SuiviBundle\Entity\Cc'));

        $builder->add('missions', 'collection', array(
            'type' => new DocTypeSuiviType,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false, //indispensable cf doc
        ));

        $builder->add('pvis', 'collection', array(
            'type' => new DocTypeSuiviType,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false, //indispensable cf doc
        ));

        $builder->add('pvr', new DocTypeSuiviType(), array('label' => 'PVR', 'data_class' => 'mgate\SuiviBundle\Entity\ProcesVerbal'));
    }

    public function getName() {
        return 'mgate_suivibundle_suivietudetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        ));
    }

}

