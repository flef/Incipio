<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use mgate\SuiviBundle\Entity\Etude;

class CommentaireSuiviType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
    }
    
    public function getName() {
        return 'mgate_suivibundle_commentairesuivitype';
    }
    
}