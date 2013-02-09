<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use mgate\PersonneBundle\Form\Type\SexeType as SexeType;
use mgate\UserBundle\Entity\UserRepository;
use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Form\EventListener\AddUserFieldSubscriber;


class PersonneType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    $builder
                ->add('prenom')
                ->add('nom')
                ->add('sexe', new SexeType())
                ->add('mobile');
            

        if(!$options['mini'] && !$options['user'])
            $builder->add('fix');
        if(!$options['mini'])
            $builder->add('adresse');

        if($options['user'])
        {
            $subscriber = new AddUserFieldSubscriber($builder->getFormFactory());
            $builder->addEventSubscriber($subscriber);
        }
            
    }

    public function getName()
    {
        return 'mgate_personnebundle_personnetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\PersonneBundle\Entity\Personne',
            'mini' => false,
            'user' => false,
            'personne' => null
        );
    }
}

