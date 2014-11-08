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
use mgate\SuiviBundle\Form\PhasesType;
use mgate\SuiviBundle\Entity\Phase;


class PhasesController extends Controller
{
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudes' => $entities,
        ));
         
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
        }
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
        
        $originalPhases = array();
        // Create an array of the current Phase objects in the database
        foreach ($etude->getPhases() as $phase) {
            $originalPhases[] = $phase;
        }
        

        $form = $this->createForm(new PhasesType, $etude, array('etude' => $etude));
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {

                if($this->get('request')->get('add'))
                {
                    $phaseNew = new Phase;
                    $phaseNew->setPosition(count($etude->getPhases()));
                    $phaseNew->setEtude($etude);
                    $etude->addPhase($phaseNew);
                }


                // filter $originalPhases to contain phases no longer present
                foreach ($etude->getPhases() as $phase) {
                    foreach ($originalPhases as $key => $toDel) {
                        if ($toDel->getId() === $phase->getId()) {
                            unset($originalPhases[$key]);
                        }
                    }
                }
                
                // remove the relationship between the phase and the etude
                foreach ($originalPhases as $phase) {
                    $em->remove($phase); // on peut faire un persist sinon, cf doc collection form
                }

                
                $em->persist( $etude ); // persist $etude / $form->getData()
                $em->flush();
                
                //Necessaire pour refraichir l ordre
                $em->refresh($etude);
                $form = $this->createForm(new PhasesType(), $etude, array('etude' => $etude));

            }
        }
        
        return $this->render('mgateSuiviBundle:Phase:phases.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
    
}
