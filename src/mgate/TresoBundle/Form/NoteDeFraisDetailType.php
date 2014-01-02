<?php
namespace mgate\TresoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\TresoBundle\Entity\NoteDeFraisDetail;



class NoteDeFraisDetailType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('description', 'textarea', 
                    array('label' => 'Description de la dépense',
                        'required' => true, 
                        'attr'=>array(
                            'cols'=>'100%', 
                            'rows'=>5)
                        )
                    )
                ->add('prixHT', 'number', array('label'=>'Prix H.T.', 'required' => false))
                ->add('tauxTVA', 'number', array('label'=>'Taux TVA', 'required' => false))
                ->add('kilometrage', 'integer', array('label'=>'Nombre de Kilomètre', 'required' => false))
                ->add('tauxKm', 'integer', array('label'=>'Prix au kilomètre (en cts)', 'required' => false))
                ->add('type', 'choice', array('choices' => NoteDeFraisDetail::getTypeChoices(), 'required' => true));
    }

    public function getName() {
        return 'mgate_tresobundle_notedefraisdetailtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\NoteDeFraisDetail',
        ));
    }
    


}