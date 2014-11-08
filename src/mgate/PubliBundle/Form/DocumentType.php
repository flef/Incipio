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