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


namespace mgate\FormationBundle\Form;

use mgate\FormationBundle\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form\PersonneType;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;

class FormationType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('titre', 'text', array('label' => 'Titre de la formation', 'required' => false,))
                ->add('description', 'textarea', array('label' => 'Description de la Formation', 'required' => true,'attr'=>array('cols'=>'100%','rows'=>5),))
                ->add('categorie', 'choice', array('multiple' => true, 'choices' => Formation::getCategoriesChoice(), 'label' => 'Catégorie', 'required' => false))
                ->add('dateDebut', 'datetime', array('label' => 'Date de debut (d/MM/y - HH:mm:ss)', 'format' => 'd/MM/y - HH:mm:ss', 'required' => false, 'widget' => 'single_text'))
                ->add('dateFin', 'datetime', array('label' => 'Date de fin (d/MM/y - HH:mm:ss)', 'format' => 'd/MM/y - HH:mm:ss', 'required' => false, 'widget' => 'single_text'))
                ->add('mandat', 'integer' )
                ->add('formateurs', 'collection', array(
                    'type' => 'genemu_jqueryselect2_entity',
                    'options' => array('label' => 'Suiveur de projet',
                        'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                        'property' => 'prenomNom',
                        'query_builder' => function(PersonneRepository $pr) {
                            return $pr->getMembreOnly();
                        },
                    'required' => false),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ))
                ->add('membresPresents', 'collection', array(
                    'type' => 'genemu_jqueryselect2_entity',
                    'options' => array('label' => 'Suiveur de projet',
                        'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                        'property' => 'prenomNom',
                        'query_builder' => function(PersonneRepository $pr) {
                            return $pr->getMembreOnly();
                        },
                        'required' => false),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ))
                ->add('docPath', 'text', array('label' => 'Lien vers des documents externes', 'required' => false,))
        ;
    }

    public function getName() {
        return 'mgate_suivibundle_formulairetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\FormationBundle\Entity\Formation',
        ));
    }

}