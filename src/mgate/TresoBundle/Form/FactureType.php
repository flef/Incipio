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

use mgate\TresoBundle\Form\FactureDetailType;
use mgate\PersonneBundle\Entity\PersonneRepository;

class FactureType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('exercice', 'integer', array('label'=>'Exercice Comptable', 'required' => true))
                ->add('numero', 'integer', array('label'=>'Numéro de la Facutre', 'required' => true))
                ->add('type', 'choice', array('choices' => \mgate\TresoBundle\Entity\Facture::getTypeChoices(), 'required' => true))
                ->add('objet', 'textarea', 
                    array('label' => 'Objet de la Facture',
                        'required' => true, 
                        'attr'=>array(
                            'cols'=>'100%', 
                            'rows'=>5)
                        )
                    )
                ->add('details', 'collection', array(
                    'type' => new FactureDetailType,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false,
                ))
                ->add('beneficiaire', 'genemu_jqueryselect2_entity', array(
                    'class' => 'mgate\PersonneBundle\Entity\Prospect',
                    'property' => 'nom',
                    'required' => true,
                    'label' => 'Facture émise pour/par',                    
                ))
                ->add('montantADeduire', new FactureDetailType, array('label'=>'Montant à déduire', 'required' => true))
                ->add('dateEmission', 'genemu_jquerydate', array('label'=>'Date d\'émission', 'required'=>true, 'widget'=>'single_text'))
                ->add('dateVersement', 'genemu_jquerydate', array('label'=>'Date de versement', 'required'=>false, 'widget'=>'single_text'));
    }

    public function getName() {
        return 'mgate_tresobundle_facturetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\Facture',
        ));
    }
    


}