<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\Prospect as Prospect;
use mgate\PersonneBundle\Entity\Personne as Personne;
use mgate\PersonneBundle\Entity\PersonneRepository as PersonneRepository;
use mgate\PersonneBundle\Form\ProspectType as ProspectType;

class EtudeType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('knownProspect', 'checkbox', array(
                'required' => false,
                'label' => "Le signataire client existe-t-il déjà dans la base de donnée ?"
                ))
             ->add('prospect', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\PersonneBundle\Entity\Prospect',
                'property' => 'nom',
                'required' => true,
                'label' => 'Prospect existant',
                ))
            ->add('newProspect', new ProspectType(), array('label' => 'Nouveau prospect:', 'required' => false))
            ->add('nom', 'text',array('label'=>'Nom interne de l\'étude'))
            ->add('description','textarea',array('label'=>'Présentation interne de l\'étude', 'required' => false))
            ->add('mandat', 'integer' )
            ->add('num', 'integer', array('label'=>'Numéro de l\'étude', 'required' => false))
            ->add('confidentiel', 'checkbox', array('label' => 'Confidentialité :', 'required' => false, 'attr'=>array("title"=>"Si l'étude est confidentielle, elle ne sera visible que par vous et les membres du CA.")))
            ->add('suiveur', 'genemu_jqueryselect2_entity', 
                array ('label' => 'Suiveur de projet',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'query_builder' => function(PersonneRepository $pr) { return $pr->getMembreOnly(); },
                       'required' => false));        
              }

    public function getName()
    {
        return 'mgate_suivibundle_etudetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\Etude',
        ));
    }
}

