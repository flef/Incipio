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


namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;

use mgate\SuiviBundle\Entity\Suivi;
use mgate\SuiviBundle\Form\SuiviType;


class SuiviController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        
        $entities = $em->getRepository('mgateSuiviBundle:Suivi')
            ->createQueryBuilder('s')
            ->innerJoin('s.etude', 'e')
            ->where('e.stateID < 5')
            //->groupBy('s.date')
            ->orderBy('e.mandat','DESC')
            ->addOrderBy('e.num', 'DESC')
            ->addOrderBy('s.date', 'DESC')
            ->getQuery()->getResult();

        return $this->render('mgateSuiviBundle:Suivi:index.html.twig', array(
            'suivis' => $entities,
        ));
         
    }  
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
            throw $this->createNotFoundException('L\'étude n\'existe pas !');

        
        
        $suivi = new Suivi;
        $suivi->setEtude($etude);
        $suivi->setDate(new \DateTime("now"));
        $form        = $this->createForm(new SuiviType, $suivi);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($suivi);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_suivi_voir', array('id' => $suivi->getId())) );
            }
                
        }
        return $this->render('mgateSuiviBundle:Suivi:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    private function compareDate(Suivi $a,Suivi $b) {
        if ($a->getDate() == $b->getDate())
            return 0;
        else
            return ($a->getDate() < $b->getDate()) ? -1 : 1;
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $suivi = $em->getRepository('mgateSuiviBundle:Suivi')->find($id);

        if (!$suivi) {
            throw $this->createNotFoundException('Ce suivi n\'existe pas !');
        }

        $etude = $suivi->getEtude();
        $suivis = $etude->getSuivis()->toArray();
        usort($suivis, array($this, 'compareDate'));

        return $this->render('mgateSuiviBundle:Suivi:voir.html.twig', array(
            'suivis'      => $suivis,
            'selectedSuivi' => $suivi,
            'etude' => $etude,
            ));
        
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $suivi = $em->getRepository('mgate\SuiviBundle\Entity\Suivi')->find($id) )
        {
            throw $this->createNotFoundException('Ce suivi n\'existe pas !');
        }

        $form        = $this->createForm(new SuiviType, $suivi);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_suivi_voir', array('id' => $suivi->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Suivi:modifier.html.twig', array(
            'form' => $form->createView(),
            'clientcontact' => $suivi,
        ));
    }
}
