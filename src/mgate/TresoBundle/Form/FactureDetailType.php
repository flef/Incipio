<?php
namespace mgate\TresoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\TresoBundle\Entity\FactureDetail;



class FactureDetailType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('description', 'textarea', 
                    array('label' => 'Description de la dépense',
                        'required' => true, 
                        'attr'=>array(
                            'cols'=>'100%', 
                            'rows'=>2)
                        )
                    )
                ->add('montantHT', 'money', array('label'=>'Prix H.T.', 'required' => false))
                ->add('tauxTVA', 'number', array('label'=>'Taux TVA (%)', 'required' => false))
                ->add('compte', 'genemu_jqueryselect2_entity', array(
                        'class' => 'mgate\TresoBundle\Entity\Compte',
                        'property' => 'libelle',
                        'required' => false,
                        'label' => 'Catégorie',
                        'configs' => array('placeholder' => 'Sélectionnez une catégorie', 'allowClear' => true),
                        ));
    }

    public function getName() {
        return 'mgate_tresobundle_facturedetailtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\FactureDetail',
        ));
    }
    


}