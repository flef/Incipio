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

use mgate\TresoBundle\Entity\FactureDetail;



class FactureDetailType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('description', 'textarea', 
                    array('label' => 'Description de la dépense',
                        'required' => false, 
                        'attr'=>array(
                            'cols'=>'100%', 
                            'rows'=>2)
                        )
                    )
                ->add('montantHT', 'money', array('label'=>'Prix H.T.', 'required' => false))
                ->add('tauxTVA', 'number', array('label'=>'Taux TVA (%)', 'required' => false))
                ->add('compte', 'genemu_jqueryselect2_entity', array(
                        'class' => 'mgate\TresoBundle\Entity\Compte',
                        'property' => 'libelle',
                        'required' => false,
                        'label' => 'Catégorie',
                        'configs' => array('placeholder' => 'Sélectionnez une catégorie', 'allowClear' => true),
                        ));
    }

    public function getName() {
        return 'mgate_tresobundle_facturedetailtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\FactureDetail',
        ));
    }
    


}