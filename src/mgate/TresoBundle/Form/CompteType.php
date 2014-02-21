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
                ->add('numero', 'text', array('label'=>'Numéro de compte', 'required' => true, 'attr' => array('maxlength' => 6,)))
                ->add('categorie', 'checkbox', array('label'=> 'Est utilisé comme catégorie ? ', 'required' => false));
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