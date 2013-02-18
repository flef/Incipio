<?php
namespace mgate\SuiviBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ValidationType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                0 => "Aucune",
                1 => "Cette phase sera soumise à une validation orale lors d’un entretien avec le client.",
                2 => "Cette phase sera soumise à une validation écrite qui prend la forme d’un Procès-Verbal Intermédiaire signé par le client."
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