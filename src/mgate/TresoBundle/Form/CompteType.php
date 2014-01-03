<?php
namespace mgate\TresoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;


class CompteType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('libelle', 'text', 
                    array('label' => 'Libellé du compte',
                        'required' => true,)
                    )
                ->add('numero', 'integer', array('label'=>'Numéro de compte', 'required' => true, 'attr' => array('min' => 100000, 'max' => 999999, 'value' => 600000)));
    }

    public function getName() {
        return 'mgate_tresobundle_comptetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\Compte',
        ));
    }
    


}