<?php

namespace mgate\PubliBundle\Form;

use mgate\PubliBundle\Entity\Document;
use mgate\PubliBundle\Form\RelatedDocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

class DocumentType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('name', 'text', array('label' => 'Nom du fichier', 'required' => false,))
                ->add('file', 'file', array('label' => 'Fichier', 'required' => true,'attr'=>array('cols'=>'100%','rows'=>5),));
        if($options['etude'] || $options['etudiant'] || $options['prospect'] || $options['formation'])
            $builder->add('relation', new RelatedDocumentType, array(
                'label' => '', 
                'etude' => $options['etude'],
                'etudiant' => $options['etudiant'],
                'prospect' => $options['prospect'],
                'formation' => $options['formation']) );
    }

    public function getName() {
        return 'mgate_suivibundle_documenttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\PubliBundle\Entity\Document',
            'etude'     => null,
            'etudiant'  => null,
            'prospect'  => null,
            'formation' => null,
        ));
    }

}