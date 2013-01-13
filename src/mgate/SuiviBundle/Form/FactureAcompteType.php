<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FactureAcompteType extends DocTypeType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('factureAcompte', new SubFaType(), array('label' => ' ', 'prospect'=>$options['prospect']));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
            'prospect' => null
        ));
    }

    public function getName()
    {
        return 'mgate_suivibundle_factureacomptetype';
    }
}


class SubFaType extends DocTypeType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        DocTypeType::buildForm($builder, $options);
    }

    public function getName() {
        return 'mgate_suivibundle_subfatype';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\FactureAcompte',
            'prospect' => '',
        );
    }

}
