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


namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;
use mgate\PersonneBundle\Form\Type\SexeType as SexeType;

class MembreType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder
                ->add('personne', new PersonneType(), array('label' => ' ', 'user' => true))
                ->add('identifiant', 'text', array('label' => 'Identifiant', 'required' => false, 'read_only' => true))
                ->add('emailEMSE', 'text', array('label' => 'Email Ecole', 'required' => false))
                ->add('promotion', 'integer', array('label' => 'Promotion', 'required' => false))
                ->add('dateDeNaissance', 'date', array('label' => 'Date de naissance (jj/mm/aaaa)', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'required' => false))
                ->add('lieuDeNaissance', 'text', array('label' => 'Lieu de naissance', 'required' => false))
				->add('nationalite', 'genemu_jqueryselect2_choice', array('label' => 'Nationalité', 'required' => false, 'preferred_choices' => array('Français'), 'choices' => array('Afghan' => 'Afghan', 'Albanais' => 'Albanais',
				'Algérien' => 'Algérien', 'Allemand' => 'Allemand', 'Américain' => 'Américain', 'Angolais' => 'Angolais', 'Argentin' => 'Argentin', 'Arménien' => 'Arménien',
				'Australien' => 'Australien', 'Autrichien' => 'Autrichien', 'Bangladais' => 'Bangladais', 'Belge' => 'Belge', 'Béninois' => 'Béninois', 'Bosniaque' => 'Bosniaque',
				'Botswanais' => 'Botswanais', 'Bhoutan' => 'Bhoutan', 'Brésilien' => 'Brésilien', 'Britannique' => 'Britannique', 'Bulgare' => 'Bulgare', 'Burkinabè' => 'Burkinabè',
				'Cambodgien' => 'Cambodgien', 'Camerounais' => 'Camerounais', 'Canadien' => 'Canadien', 'Chilien' => 'Chilien', 'Chinois' => 'Chinois', 'Colombien' => 'Colombien',
				'Congolais' => 'Congolais', 'Cubain' => 'Cubain', 'Danois' => 'Danois', 'Ecossais' => 'Ecossais', 'Egyptien' => 'Egyptien', 'Espagnol' => 'Espagnol', 'Estonien' => 'Estonien',
				'Européen' => 'Européen', 'Finlandais' => 'Finlandais', 'Français' => 'Français', 'Gabonais' => 'Gabonais', 'Georgien' => 'Georgien', 'Grec' => 'Grec', 'Guinéen' => 'Guinéen',
				'Haïtien' => 'Haïtien', 'Hollandais' => 'Hollandais', 'Hong-Kong' => 'Hong-Kong', 'Hongrois' => 'Hongrois', 'Indien' => 'Indien', 'Indonésien' => 'Indonésien', 'Irakien' => 'Irakien',
				'Iranien' => 'Iranien', 'Irlandais' => 'Irlandais', 'Islandais' => 'Islandais', 'Israélien' => 'Israélien', 'Italien' => 'Italien', 'Ivoirien' => 'Ivoirien', 'Jamaïcain' => 'Jamaïcain',
				'Japonais' => 'Japonais', 'Kazakh' => 'Kazakh', 'Kirghiz' => 'Kirghiz', 'Kurde' => 'Kurde', 'Letton' => 'Letton', 'Libanais' => 'Libanais', 'Liechtenstein' => 'Liechtenstein',
				'Lituanien' => 'Lituanien', 'Luxembourgeois' => 'Luxembourgeois', 'Macédonien' => 'Macédonien', 'Madagascar' => 'Madagascar', 'Malaisien' => 'Malaisien', 'Malien' => 'Malien',
				'Maltais' => 'Maltais', 'Marocain' => 'Marocain', 'Mauritanien' => 'Mauritanien', 'Mauricien' => 'Mauricien', 'Mexicain' => 'Mexicain', 'Monégasque' => 'Monégasque', 'Mongol' => 'Mongol',
				'Néo-Zélandais' => 'Néo-Zélandais', 'Nigérien' => 'Nigérien', 'Nord-Coréen' => 'Nord-Coréen', 'Norvégien' => 'Norvégien', 'Pakistanais' => 'Pakistanais', 'Palestinien' => 'Palestinien',
				'Péruvien' => 'Péruvien', 'Philippins' => 'Philippins', 'Polonais' => 'Polonais', 'Portoricain' => 'Portoricain', 'Portugais' => 'Portugais', 'Roumain' => 'Roumain', 'Russe' => 'Russe',
				'Sénégalais' => 'Sénégalais', 'Serbe' => 'Serbe', 'Serbo-croate' => 'Serbo-croate', 'Singapour' => 'Singapour', 'Slovaque' => 'Slovaque', 'Soviétique' => 'Soviétique', 'Sri-lankais' => 'Sri-lankais',
				'Sud-Africain' => 'Sud-Africain', 'Sud-Coréen' => 'Sud-Coréen', 'Suédois' => 'Suédois', 'Suisse' => 'Suisse', 'Syrien' => 'Syrien', 'Tadjik' => 'Tadjik', 'Taïwanais' => 'Taïwanais', 'Tchadien' => 'Tchadien',
				'Tchèque' => 'Tchèque', 'Thaïlandais' => 'Thaïlandais', 'Tunisien' => 'Tunisien', 'Turc' => 'Turc', 'Ukrainien' => 'Ukrainien', 'Uruguayen' => 'Uruguayen', 'Vénézuélien' => 'Vénézuélien', 'Vietnamien' => 'Vietnamien')))
                ->add('appartement', 'integer', array('label' => 'Appartement', 'required' => false))
                ->add('mandats', 'collection', array(
                    'type' => new MandatType,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false, //indispensable cf doc
                ))
                ->add('dateConventionEleve', 'genemu_jquerydate', array('label' => 'Date de Signature de la Convention Elève', 'format' => 'dd/MM/yyyy', 'required' => false, 'widget' => 'single_text'))
                ->add('photo', 'file', array(
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Modifier la photo de profil du membre',
                ));
                        
    }

    public function getName() {
        return 'mgate_personnebundle_membretype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\PersonneBundle\Entity\Membre',
        ));
    }

}

