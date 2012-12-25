<?php
namespace mgate\SuiviBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PrestationType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'info' => 'ingénieur informatique',
                'elec' => 'ingénieur électronique',
                'info-elec' => 'ingénieur informatique et électronique',
                'micro'=> 'ingénieur microélectronique',
            )
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'typePrestation';
    }
}