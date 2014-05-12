<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
                    'query_builder' => function(PersonneRepository $pr) {
                        return $pr->getMembreOnly();
                    },
                    'required' => false))
                ->add('ap', new SubApType(), array('label' => ' ', 'prospect'=>$options['prospect']))
                ->add('fraisDossier', 'integer', array('label' => 'Frais de dossier', 'required' => false))
                ->add('presentationProjet', 'textarea', array('label' => 'Présentation du projet', 'required' => false, 'attr'=>array('cols'=>'100%', 'rows'=>5)))
                ->add('descriptionPrestation', 'textarea', array('label' => 'Description de la prestation proposée', 'required' => false, 'attr'=>array("title"=>"La phrase commence par 'La prestation proposée par M-GaTE consiste à réaliser ...'. Il faut continuer la continuer en décrivant la prestation proposée. Le début de la phrase est déjà généré.", 'cols'=>'100%', 'rows'=>5)))
                ->add('typePrestation', 'choice', array('choices'=>Etude::getTypePrestationChoice(), 'label' => 'Type de prestation', 'required' => false))
                ->add('competences', 'textarea', array('label' => 'Capacité des intervenants:', 'required' => false, 'attr'=>array('cols'=>'100%', 'rows'=>5)));
    }

    public function getName() {
        return 'mgate_suivibundle_aptype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
            'prospect' => '',
        ));
    }

}

class SubApType extends DocTypeType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('contactMgate', 'genemu_jqueryselect2_entity', array('label' => "'En cas d’absence ou de problème, il est également possible de joindre ...' ex: Vice-Président",
            'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
            'property' => 'prenomNom',
            'attr' => array('title' => "Dans l'AP: 'En cas d’absence ou de problème, il est également possible de joindre le ...'"),
            'query_builder' => function(PersonneRepository $pr) {
                return $pr->getMembresByPoste("%vice-president%");
            },
            'required' => true));
        DocTypeType::buildForm($builder, $options);
        $builder->add('nbrDev', 'integer', array('label' => 'Nombre de developpeurs estimé', 'required' => false, 'attr' => array('title' => 'Mettre 0 pour ne pas afficher la phrase indiquant le nombre d\'intervenant')));
        
    }

    public function getName() {
        return 'mgate_suivibundle_subaptype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Ap',
            'prospect' => '',
        ));
    }

}
