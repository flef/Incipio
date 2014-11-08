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


namespace mgate\CommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('mgateCommentBundle:Default:index.html.twig', array('name' => $name));
    }
    
    
    public function maintenanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $etude = new \mgate\SuiviBundle\Entity\Etude;
        $etudes = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        foreach ($etudes as $entity) {
            if(!$em->getRepository('mgateCommentBundle:Thread')->findBy(array('id'=>$entity)))
            $this->container->get('mgate_comment.thread')->creerThread("etude_", $this->container->get('router')->generate('mgatePersonne_prospect_voir', array('id' => $entity->getId())), $entity);
        }
        
        
        return $this->render('mgateCommentBundle:Default:index.html.twig', array('name' => 'rien'));
    }
}
