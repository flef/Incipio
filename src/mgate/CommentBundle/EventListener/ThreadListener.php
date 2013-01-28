<?php
namespace mgate\CommentBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use mgate\PersonneBundle\Entity\Prospect as Prospect;

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
            echo "LLLLLLLLLLLLLLLLLLLLLAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA";
            
            $this->container->get('mgate_comment.thread')->creerThread("prospect_", $this->container->get('router')->generate('mgatePersonne_prospect_voir', array('id' => $entity->getId())), $entity);
        }
    }
}