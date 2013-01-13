<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Entity\Ap;
use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\Type\PrestationType as PrestationType;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;

class ApType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('suiveur', 'entity', array('label' => 'Suiveur de projet',
                    'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                    'property' => 'prenomNom',
                    'property_path' => true,
                    'query_builder' => function(PersonneRepository $pr) {
                        return $pr->getMembreOnly();
                    },
                    'required' => false))
                ->add('ap', new SubApType(), array('label' => ' ', 'prospect'=>$options['prospect']))
                ->add('fraisDossier', 'integer', array('label' => 'Frais de dossier', 'required' => false))
                ->add('presentationProjet', 'textarea', array('label' => 'Présentation du projet', 'required' => false))
                ->add('descriptionPrestation', 'textarea', array('label' => 'Description de la prestation proposée par M-GaTE', 'required' => false))
                ->add('typePrestation', new PrestationType(), array('label' => 'Type de prestation', 'required' => false))
                ->add('competences', 'textarea', array('label' => 'Capacité des intervenants:', 'required' => false));
    }

    public function getName() {
        return 'mgate_suivibundle_aptype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
            'prospect' => '',
        );
    }

}

class SubApType extends DocTypeType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        DocTypeType::buildForm($builder, $options);
        $builder->add('nbrDev', 'integer', array('label' => 'Nombre de developpeurs estimé', 'required' => false));
    }

    public function getName() {
        return 'mgate_suivibundle_subaptype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Ap',
            'prospect' => '',
        );
    }

}
