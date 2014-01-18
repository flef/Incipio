<?php
namespace mgate\TresoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Entity\PersonneRepository;
use mgate\SuiviBundle\Entity\EtudeRepository;

class BVType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder
            ->add('mandat', 'integer')
            ->add('numero', 'integer')
            ->add('etudiant','genemu_jqueryselect2_entity',array (
                      'label' => 'Etudiant',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'query_builder' => function(PersonneRepository $pr) {
                            return $pr->getMembreOnly();
                        },
                       'required' => true))
            ->add('nombreJEH', 'integer')
            ->add('remunerationBruteParJEH', 'money')
            ->add('dateDeVersement', 'genemu_jquerydate', array('label'=>'Date de versement', 'required'=>true, 'widget'=>'single_text'))
            ->add('typeDeTravail', 'text')
            ->add('etude','genemu_jqueryselect2_entity',array (
                      'label' => 'Etudiant',
                       'class' => 'mgate\\SuiviBundle\\Entity\\Etude',
                       'property' => 'reference',                      
                       'required' => true))
            //->add('mission')
            ->add('baseURSSAF', 'money')
            ->add('numeroVirement', 'text')
            ->add('tauxJuniorAssietteDeCotisation', 'percent', array('precision' => 2))
            ->add('tauxJuniorRemunerationBrute', 'percent', array('precision' => 2))
            ->add('tauxEtudiantAssietteDeCotisation', 'percent', array('precision' => 2))
            ->add('tauxEtudiantRemunerationBrute', 'percent', array('precision' => 2));
    }

    public function getName() {
        return 'mgate_tresobundle_bvtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\BV',
        ));
    }
    


}