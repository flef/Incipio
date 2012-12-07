<?php

namespace mgate\TestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use mgate\TestBundle\Form\TestThreadType;

class SubclassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('champ1')
            ->add('mapped1')
            ->add('mapped2')
            //->add('mappedRelated1', new RelatedclassType) // délicat
            ->add('thread', new TestThreadType) // délicat 
            ->add('mappedRelated1', 'entity', 
                array ('label' => 'NomDuLabel',
                       'class' => 'mgate\\TestBundle\\Entity\\Relatedclass',
                       'property' => 'champ1',
                       'required' => true))   
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TestBundle\Entity\Subclass'
        ));
    }

    public function getName()
    {
        return 'mgate_testbundle_subclasstype';
    }
}
