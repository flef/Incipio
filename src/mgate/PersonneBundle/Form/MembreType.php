<?php

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
                ->add('promotion', 'integer', array('label' => 'Promotion', 'required' => false))
                ->add('dateDeNaissance', 'date', array('label' => 'Date de naissance (jj/mm/aaaa)', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'required' => false))
                ->add('lieuDeNaissance', 'text', array('label' => 'Lieu de naissance', 'required' => false))
				->add('nationalite', 'genemu_jqueryselect2_choice', array('label' => 'Nationalité', 'required' => false, 'choices' => array('Afghan' => 'Afghan', 'Albanais' => 'Albanais',
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

