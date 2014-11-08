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
use mgate\SuiviBundle\Form\GroupesPhasesType;
use mgate\SuiviBundle\Entity\GroupePhases;
use \mgate\SuiviBundle\Form\GroupePhasesType;


class GroupePhasesController extends Controller
{
        

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id))
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
			
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
        
        $originalGroupes = array();
        // Create an array of the current groupe objects in the database
        foreach ($etude->getGroupes() as $groupe) {
            $originalGroupes[] = $groupe;
        }
        

        $form = $this->createForm(new GroupesPhasesType, $etude);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {

                if($this->get('request')->get('add'))
                {
                    $groupeNew = new GroupePhases;
                    $groupeNew->setNumero(count($etude->getGroupes()));
                    $groupeNew->setTitre("Titre")->setDescription("Description");
                    $groupeNew->setEtude($etude);
                    $etude->addGroupe($groupeNew);
                }


                // filter $originalGroupes to contain Groupes no longer present
                foreach ($etude->getGroupes() as $groupe) {
                    foreach ($originalGroupes as $key => $toDel) {
                        if ($toDel->getId() === $groupe->getId()) {
                            unset($originalGroupes[$key]);
                        }
                    }
                }
                
                // remove the relationship between the groupe and the etude
                foreach ($originalGroupes as $groupe) {
                    $em->remove($groupe); // on peut faire un persist sinon, cf doc collection form
                }

                
                $em->persist( $etude ); // persist $etude / $form->getData()
                $em->flush();
                
                //Necessaire pour refraichir l ordre
                $em->refresh($etude);
                $form = $this->createForm(new GroupesPhasesType, $etude);
            }
        }
        
        return $this->render('mgateSuiviBundle:GroupePhases:modifier.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
    
}
