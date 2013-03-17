<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use mgate\PersonneBundle\Form\Type\SexeType;
use mgate\UserBundle\Entity\UserRepository;
use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Form\EventListener\AddUserFieldSubscriber;


class PersonneType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        if($options['user'])
        {
            $helpEmail = array('title'=>"Pas d'adresse etu. Cette adresse est reprise dans les AP des études suivies.");
            $helpMobile = array('title'=>'Sous la forme: 06 78 39 .. Ce téléphone est repris dans les AP des études suivies.');
        }
        else
        {
            $helpEmail = array();
            $helpMobile = array();
        }
        
	    $builder
                ->add('prenom')
                ->add('nom')
                ->add('sexe', new SexeType())
                ->add('mobile', 'text', array('required'=>false, 'attr'=>$helpEmail))
                ->add('email', 'email', array('required'=>false, 'attr'=>$helpMobile));
            

        if(!$options['mini'] && !$options['user'])
            $builder->add('fix', 'text', array('required'=>false));
        if(!$options['mini'])
            $builder->add('adresse', 'text', array('required'=>false, 'attr'=>array('title'=>'Sous la forme: Appartement 3114, 879 Route de Mimet, 13120 Gardanne')));
        
        if($options['user'])
        {
            //Finalement non, on associe dans ce sens la
            //Parce que la situation la plus commune sera :
            // membre créé en premier, compte ensuite
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
            'signataire' => false
        );
    }
}

