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

namespace mgate\TresoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

class CotisationURSSAFType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder
            ->add('libelle','text',array('label'=>'Libelle'))
            ->add('dateDebut', 'genemu_jquerydate', array('label'=>'Applicable du', 'required'=>true, 'widget'=>'single_text'))
            ->add('dateFin', 'genemu_jquerydate', array('label'=>'Applicable au', 'required'=>true, 'widget'=>'single_text'))
            ->add('tauxPartJE', 'percent',array('label'=>'Taux Part Junior', 'required' => false, 'precision' => 2))
            ->add('tauxPartEtu', 'percent',array('label'=>'Taux Part Etu', 'required' => false, 'precision' => 2))
            ->add('isSurBaseURSSAF', 'checkbox', array('label'=>'Est indexé sur la base URSSAF ?', 'required'=>false))
            ->add('deductible', 'checkbox', array('label'=>'Est déductible ?', 'required'=>false));
    }

    public function getName() {
        return 'mgate_tresobundle_cotisationurssaftype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\CotisationURSSAF',
        ));
    }
}