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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use mgate\PersonneBundle\Form\Type\SexeType;
use mgate\UserBundle\Entity\UserRepository;
use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Form\EventListener\AddUserFieldSubscriber;

class PersonneType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        if ($options['user']) {
            $helpEmail = array('title' => "Pas d'adresse etu. Cette adresse est reprise dans les AP des études suivies.");
            $helpMobile = array('title' => 'Sous la forme: 06 78 39 .. Ce téléphone est repris dans les AP des études suivies.');
        } else {
            $helpEmail = array();
            $helpMobile = array();
        }

        $builder
                ->add('prenom')
                ->add('nom')
                ->add('sexe', new SexeType())
                ->add('mobile', 'text', array('required' => false, 'attr' => $helpMobile))
                ->add('email', 'email', array('required' => false, 'attr' => $helpEmail))
                ->add('estAbonneNewsletter', 'checkbox', array('label' => 'Abonné Newsletter ?', 'required' => false))
                ->add('emailEstValide', 'checkbox', array('label' => 'Email Valide ?', 'required' => false));

        if (!$options['mini'] && !$options['user'])
            $builder->add('fix', 'text', array('required' => false));
        if (!$options['mini'])
            $builder->add('adresse', 'text', array('required' => false, 'attr' => array('title' => 'Sous la forme: Appartement 3114, 879 Route de Mimet, 13120 Gardanne')));
    }

    public function getName() {
        return 'mgate_personnebundle_personnetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\PersonneBundle\Entity\Personne',
            'mini' => false,
            'user' => false,
            'signataire' => false
        ));
    }

}

