<?php
// src/MyProject/MyBundle/Entity/TestComment.php

namespace mgate\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class TestComment extends BaseComment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Thread of this comment
     *
     * @var TestThread
     * @ORM\ManyToOne(targetEntity="mgate\TestBundle\Entity\TestThread")
     */
    protected $thread;
}