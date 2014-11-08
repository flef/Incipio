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

use mgate\PersonneBundle\Entity\PersonneRepository;

class BVType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder
            ->add('mandat', 'integer')
            ->add('numero', 'integer')
            ->add('nombreJEH', 'integer')
            ->add('remunerationBruteParJEH', 'money')
            ->add('dateDeVersement', 'genemu_jquerydate', array('label'=>'Date de versement', 'required'=>true, 'widget'=>'single_text'))
            ->add('dateDemission', 'genemu_jquerydate', array('label'=>'Date d\'émission', 'required'=>true, 'widget'=>'single_text'))
            ->add('typeDeTravail', 'text')            
            ->add('mission','genemu_jqueryselect2_entity',array (
                      'label' => 'Mission',
                       'class' => 'mgate\\SuiviBundle\\Entity\\Mission',
                       'property' => 'reference',                      
                       'required' => true))            
            ->add('numeroVirement', 'text', array('label'=>'Numéro de Virement', 'required'=>false));            
    }

    public function getName() {
        return 'mgate_tresobundle_bvtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\BV',
        ));
    }
    


}