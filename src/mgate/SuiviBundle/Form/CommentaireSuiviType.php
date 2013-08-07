<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use mgate\SuiviBundle\Entity\Etude;

class CommentaireSuiviType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        //$builder->add('stateDescription', 'textarea', array( 'label'=>'Objectif', 'required'=>false));
    }
    
    public function getName() {
        return 'mgate_suivibundle_commentairesuivitype';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        );
    }
    
}