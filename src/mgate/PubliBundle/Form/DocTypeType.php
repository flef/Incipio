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


namespace mgate\PubliBundle\Form;

use mgate\PubliBundle\Form\RelatedDocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;
use mgate\PubliBundle\Controller\TraitementController;

class DocTypeType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add(  'name', 'choice', array(
                        'required' => true,
                        'label' => "Document Type",
                        'choices' => array(
                            TraitementController::DOCTYPE_SUIVI_ETUDE                   => 'Fiche de suivi d\'étude',
                            TraitementController::DOCTYPE_DEVIS                         => 'Devis',
                            TraitementController::DOCTYPE_AVANT_PROJET                  => 'Avant-Projet',
                            TraitementController::DOCTYPE_CONVENTION_CLIENT             => 'Convention Client',
                            TraitementController::DOCTYPE_FACTURE_ACOMTE                => 'Facture d\'acompte',
                            TraitementController::DOCTYPE_FACTURE_INTERMEDIAIRE         => 'Facture intermédiaire',
                            TraitementController::DOCTYPE_FACTURE_SOLDE                 => 'Facture de solde',
                            TraitementController::DOCTYPE_PROCES_VERBAL_INTERMEDIAIRE   => 'Procès verbal de recette intermédiaire',
                            TraitementController::DOCTYPE_PROCES_VERBAL_FINAL           => 'Procès verbal de recette final',
                            TraitementController::DOCTYPE_RECAPITULATIF_MISSION         => 'Récapitulatif de mission',
                            TraitementController::DOCTYPE_DESCRIPTIF_MISSION            => 'Descriptif de mission',
                            TraitementController::DOCTYPE_CONVENTION_ETUDIANT           => 'Convention Etudiant',
                            TraitementController::DOCTYPE_FICHE_ADHESION                => 'Fiche d\'adhésion',
                            TraitementController::DOCTYPE_ACCORD_CONFIDENTIALITE        => 'Accord de confidentialité',
                            TraitementController::DOCTYPE_DECLARATION_ETUDIANT_ETR      => 'Déclaration étudiant étranger',
                            TraitementController::DOCTYPE_NOTE_DE_FRAIS                 => 'Note de Frais',
                            )))
                ->add('etudiant', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\\PersonneBundle\\Entity\\Membre',
                'property' => 'identifiant',
                'label' => 'Intervenant pour vérifier le template',
                'required' => false
                ))
             ->add('template', 'file', array('required' => true))
             ->add('etude','genemu_jqueryselect2_entity',array (
                       'label' => 'Etude pour vérifier le template',
                        'class' => 'mgate\\SuiviBundle\\Entity\\Etude',
                        'property' => 'reference',                      
                        'required' => false))
             ->add('verification', 'checkbox', array('label' => 'Activer la vérification', 'required' => false));
    }

    public function getName() {
        return 'mgate_suivibundle_doctypetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }

}