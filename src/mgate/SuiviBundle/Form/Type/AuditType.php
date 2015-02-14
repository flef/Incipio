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

namespace mgate\SuiviBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

class AuditType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
            'choices' => array(
                'n' => 'Non audité',
                'e' => 'Exhaustive',
                'd' => 'Déontologique',
            )
        ));
    }

    public function getParent(array $options)
    {
        return 'choice';
    }

    public function getName()
    {
        return 'auditType';
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('gender_code', new GenderType(), array(
            'placeholder' => 'Type d\'audit',
        ));
    }
}