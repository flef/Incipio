<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


namespace mgate\CommentBundle\Manager;

use FOS\CommentBundle\Acl\AclThreadManager as FOSthread;
use Doctrine\ORM\EntityManager;
use mgate\CommentBundle\Entity\Thread as mgateThread;


class ThreadManager
{
    protected $tm;
    protected $em;
    
    public function __construct( FOSthread $threadManager, EntityManager $entitymanager)
    {
        $this->tm = $threadManager;
        $this->em = $entitymanager;
    }
    
    public function creerThread($name, $permaLink, $entity)
    {
         
        
        if(!$entity->getThread())
        {

            //get('fos_comment.manager.thread')
            //$thread = new mgateThread;

            $thread = $this->tm->createThread($name.$entity->getId());
            //$thread->setId($name.$entity->getId());
            //$thread->setPermalink( $permaLink );
            $entity->setThread($thread);
            //$this->em->persist($thread); 

            $this->em->flush();
        }
    }
}
