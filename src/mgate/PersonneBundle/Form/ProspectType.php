<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\CommentBundle\Form\ThreadType;

class ProspectType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
            ->add('nom', 'text')
            ->add('entite', 'text', array('required'=>false))
            ->add('adresse', 'text', array('required'=>false));
            
    }

    public function getName()
    {
        return 'alex_suivibundle_etudetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
            'data_class' => 'mgate\PersonneBundle\Entity\Prospect',
        ));
    }
}

