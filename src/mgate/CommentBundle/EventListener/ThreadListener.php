<?php
namespace mgate\CommentBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use mgate\PersonneBundle\Entity\Prospect as Prospect;
use mgate\PersonneBundle\Entity\Prospect as Etude;

class ThreadListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Prospect)
        {
            $this->container->get('mgate_comment.thread')->creerThread("prospect_", $this->container->get('router')->generate('mgatePersonne_prospect_voir', array('id' => $entity->getId())), $entity);
        }
        elseif ($entity instanceof Etude)
        {
            $this->container->get('mgate_comment.thread')->creerThread("etude_", $this->container->get('router')->generate('mgateSuivi_etude_voir', array('id' => $entity->getId())), $entity);
        }
    }
}