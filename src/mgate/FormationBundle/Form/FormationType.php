<?php

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
                ->add('description', 'textarea', array('label' => 'Description de la Formation', 'required' => false,))
                ->add('categorie', 'choice', array('multiple' => true, 'choices' => Formation::getCategoriesChoice(), 'label' => 'Catégorie', 'required' => false))
                ->add('dateDebut', 'datetime', array('label' => 'Date de debut (d/MM/y - HH:mm:ss)', 'format' => 'd/MM/y - HH:mm:ss', 'required' => false, 'widget' => 'single_text'))
                ->add('dateFin', 'datetime', array('label' => 'Date de fin (d/MM/y - HH:mm:ss)', 'format' => 'd/MM/y - HH:mm:ss', 'required' => false, 'widget' => 'single_text'))
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