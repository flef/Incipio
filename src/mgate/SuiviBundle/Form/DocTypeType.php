<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\PersonneBundle\Form;
use mgate\PersonneBundle\Entity\PersonneRepository;
use mgate\PersonneBundle\Form\PersonneType;
use mgate\PersonneBundle\Form\EmployeType;
use mgate\PersonneBundle\Entity\Prospect;
use mgate\PersonneBundle\Entity\Personne;

class DocTypeType extends AbstractType
{
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        // Version du document
        $builder->add('version', 'integer', array('label'=>'Version du document'));
        
        $builder->add('signataire1', 'genemu_jqueryselect2_entity', 
            array ('label' => 'Signataire Junior',
                   'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                   'property' => 'prenomNom',
                   'query_builder' => function(PersonneRepository $pr) { return $pr->getMembresByPoste('president%'); },
                   'required' => true));
       
        
        
        // Si le document n'est ni une FactureVente ni un RM
        if($options['data_class']!='mgate\SuiviBundle\Entity\Mission' ) // le signataire 2 est l'intervenant
        {
            $pro=$options['prospect'];
            if($options['data_class']!='mgate\SuiviBundle\Entity\Av'){
                $builder->add('knownSignataire2', 'checkbox', array(
                    'required' => false,
                    'label' => "Le signataire client existe-t-il déjà dans la base de donnée ?"
                    ))             
                ->add('newSignataire2', new EmployeType(), array('label' => 'Nouveau signataire '.$pro->getNom(), 'required' => false, 'signataire' => true, 'mini' => true) );   
            }
            $builder->add('signataire2', 'genemu_jqueryselect2_entity', array(
                'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                'property' => 'prenomNom',
                'label' => 'Signataire '.$pro->getNom(),
                'query_builder' => function(PersonneRepository $pr) use ($pro) { return $pr->getEmployeOnly($pro); },
                'required' => false
                ));
        }

                               
            $builder->add('dateSignature', 'genemu_jquerydate', array('label'=>'Date de Signature du document', 'required'=>false,'format' => 'dd/MM/yyyy', 'widget'=>'single_text'));
            
    }
    
    public function getName()
    {
        return 'mgate_suivibundle_doctypetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
            $resolver->setDefaults(array(
            'data_class' => 'mgate\SuiviBundle\Entity\DocType',
            'prospect' => '',
        ));
    }
}


