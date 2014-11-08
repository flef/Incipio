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
use mgate\CommentBundle\Form\ThreadType;



class SuiviType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder->add('date','date',array('label'=>'Date du suivi'))
                ->add('etat','textarea',array('label'=>'Etat de l\'étude','attr'=>array('cols'=>'100%','rows'=>5)))
                ->add('todo','textarea',array('label'=>'Taches à faire', 'attr'=>array('cols'=>'100%','rows'=>5)));
    }

    public function getName()
    {
        return 'mgate_suivibundle_clientcontacttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
            $resolver->setDefaults(array(
                'data_class' => 'mgate\SuiviBundle\Entity\Suivi',
            ));
    }
}


