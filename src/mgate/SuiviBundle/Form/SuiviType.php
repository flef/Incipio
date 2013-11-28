<?php

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
                ->add('etat','text',array('label'=>'Etat de l\'étude','attr'=>array('cols'=>'100%','rows'=>5)))
                ->add('todo','textarea',array('label'=>'Taches à faire', 'attr'=>array('cols'=>'100%','rows'=>5)));
    }

    public function getName()
    {
        return 'mgate_suivibundle_clientcontacttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
            $resolver->setDefaults(array(
                'data_class' => 'mgate\SuiviBundle\Entity\ClientContact',
            ));
    }
}


