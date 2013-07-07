<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Entity\Ap;
use mgate\SuiviBundle\Entity\Etude;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;

class ApType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('suiveur', 'genemu_jqueryselect2_entity', array('label' => 'Suiveur de projet',
                    'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                    'property' => 'prenomNom',
                    'property_path' => true,
                    'query_builder' => function(PersonneRepository $pr) {
                        return $pr->getMembreOnly();
                    },
                    'required' => false))
                ->add('ap', new SubApType(), array('label' => ' ', 'prospect'=>$options['prospect']))
                ->add('fraisDossier', 'integer', array('label' => 'Frais de dossier', 'required' => false))
                ->add('presentationProjet', 'textarea', array('label' => 'Présentation du projet', 'required' => false, 'attr'=>array("title"=>"La phrase commence par 'Dans le cadre de son activité professionnelle, \"NomDuClient\" ... '. Il faut la continuer en décrivant le projet. Le début de la phrase est déjà généré.")))
                ->add('descriptionPrestation', 'textarea', array('label' => 'Description de la prestation proposée par M-GaTE', 'required' => false, 'attr'=>array("title"=>"La phrase commence par 'La prestation proposée par M-GaTE consiste à réaliser ...'. Il faut continuer la continuer en décrivant la prestation proposée. Le début de la phrase est déjà généré.")))
                ->add('typePrestation', 'choice', array('choices'=>Etude::getTypePrestationChoice(), 'label' => 'Type de prestation', 'required' => false))
                ->add('competences', 'textarea', array('label' => 'Capacité des intervenants:', 'required' => false, 'attr'=>array("title"=>"La phrase commence par 'Les réalisateurs de cette étude devront donc être capables :'. Listez donc les capacités des intervenants sous la forme '- maitriser HTML/CSS', avec des sauts à la ligne.")));
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
        $builder->add('contactMgate', 'genemu_jqueryselect2_entity', array('label' => "'En cas d’absence ou de problème, il est également possible de joindre ...' ex: Vice-Président",
            'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
            'property' => 'prenomNom',
            'property_path' => true,
            'attr' => array('title' => "Dans l'AP: 'En cas d’absence ou de problème, il est également possible de joindre le ...'"),
            'query_builder' => function(PersonneRepository $pr) {
                return $pr->getPresidents();
            },
            'required' => true));
        DocTypeType::buildForm($builder, $options);
        $builder->add('nbrDev', 'integer', array('label' => 'Nombre de developpeurs estimé', 'required' => false, 'attr' => array('title' => 'Mettre 0 pour ne pas afficher la phrase indiquant le nombre d\'intervenant')));
        
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
