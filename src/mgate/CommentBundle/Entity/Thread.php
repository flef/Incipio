<?php

namespace mgate\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Thread as BaseThread;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Thread extends BaseThread
{
    /**
     * @var integer $id
     *
     * ORM\Column(name="id", type="integer")
     * @ORM\Column(type="string")
     * @ORM\Id
     * ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}