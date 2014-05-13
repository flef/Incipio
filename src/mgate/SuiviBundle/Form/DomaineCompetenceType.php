<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form;
use mgate\SuiviBundle\Entity\DomaineCompetence;

class DomaineCompetenceType  extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('nom', 'text');
    }

    public function getName() {
        return 'mgate_suivibundle_domainecompetencetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\DomaineCompetence',
        ));
    }

}

