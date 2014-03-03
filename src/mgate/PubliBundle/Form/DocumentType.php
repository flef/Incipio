<?php

namespace mgate\PubliBundle\Form;

use mgate\PubliBundle\Entity\Document;
use mgate\PubliBundle\Form\CategorieDocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

class DocumentType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('name', 'text', array('label' => 'Titre de la formation', 'required' => false,))
                ->add('file', 'file', array('label' => 'Description de la Document', 'required' => true,'attr'=>array('cols'=>'100%','rows'=>5),));
        if($options['etude'] || $options['etudiant'] || $options['prospect'] || $options['formation'])
        $builder->add('categorie', new CategorieDocumentType, array('label' => '') );
    }

    public function getName() {
        return 'mgate_suivibundle_documenttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\PubliBundle\Entity\Document',
            'etude'     => false,
            'etudiant'  => false,
            'prospect'  => false,
            'formation' => false,
        ));
    }

}