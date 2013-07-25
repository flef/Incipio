<?php

namespace mgate\FormationBundle\Form;
use mgate\FormationBundle\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form\PersonneType;

class FormationType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('titre', 'text', array('label' => 'Présentation du projet', 'required' => false,))
                ->add('description', 'textarea', array('label' => 'Description de la Formation', 'required' => false,))
                ->add('categorie', 'choice', array('choices' => Formation::getCategoriesChoice(), 'label' => 'Catégorie', 'required' => false))
                ->add('dateDebut', 'genemu_jquerydate', array('label' => 'Date de debut', 'format' => 'd/MM/y', 'required' => false, 'widget' => 'single_text'))
                ->add('dateFin', 'genemu_jquerydate', array('label' => 'Date de fin', 'format' => 'd/MM/y', 'required' => false, 'widget' => 'single_text'))
                ->add('formateurs', 'collection', array(
                        'type' => new PersonneType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                    ))
                ->add('membresPresents', 'collection', array(
                        'type' => new PersonneType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                    ));
    }

    public function getName() {
        return 'mgate_suivibundle_formulairetype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\FormationBundle\Entity\Formation',
            'prospect' => '',
        );
    }

}