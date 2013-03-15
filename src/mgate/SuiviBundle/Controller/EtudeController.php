<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\DocTypeSuiviType;
use mgate\SuiviBundle\Form\SuiviType;

//use mgate\UserBundle\Entity\User;

class EtudeController extends Controller
{
 
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page)
    {
        $MANDAT_MAX = $this->get('mgate.etude_manager')->getMaxMandat();
        
        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.context')->getToken()->getUser()->getPersonne();
        
        //Etudes Suiveur
        $etudesSuiveur = array();
        foreach($em->getRepository('mgateSuiviBundle:Etude')->findBy(array('suiveur' => $user), array('mandat'=> 'DESC', 'num'=> 'DESC')) as $etude)
        {
            $stateID = $etude->getStateID();
            if( $stateID <= 1 )
             array_push($etudesSuiveur, $etude);
        }
        
        
        //Etudes En Cours : stateID = 0||1
        $etudesEnCours = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => 1), array('mandat'=> 'DESC', 'num'=> 'DESC'));
        /*foreach($em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => 1), array('mandat'=> 'DESC', 'num'=> 'DESC')) as $etude)
        {
            array_push($etudesEnCours, $etude);
        }*/

        //Etudes en pause : stateID = 2
        $etudesEnPause = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => 2),array('mandat'=> 'DESC', 'num' => 'DESC'));

        
        //Etudes Avortees : stateID = 3
        $etudesAvorteesParMandat = array();
        for($i = 1; $i < $MANDAT_MAX; $i++)
            array_push ($etudesAvorteesParMandat,$em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => 3,'mandat' => $i),array('num' => 'DESC')));
        
        //Etudes Terminees : stateID = 4
        $etudesTermineesParMandat = array();
        for($i = 1; $i < $MANDAT_MAX; $i++)
            array_push ($etudesTermineesParMandat,$em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => 4, 'mandat' => $i), array('num' => 'DESC')));
            
        
        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudesEnCours' => $etudesEnCours,
            'etudesSuiveur' => $etudesSuiveur,
            'etudesEnPause' => $etudesEnPause,
            'etudesTermineesParMandat' => $etudesTermineesParMandat,
            'etudesAvorteesParMandat' => $etudesAvorteesParMandat,
        ));
         
    }
    
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function stateAction()
    {
        
        $em = $this->getDoctrine()->getManager();

        $stateDescription = isset($_POST['state']) ? $_POST['state'] : "";
        $stateID = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $etudeID = isset($_POST['etude']) ? intval($_POST['etude']) : 0;
        
            if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($etudeID)) {
                throw $this->createNotFoundException('Etude[id=' . $etudeID . '] inexistant');
            } else {

                $etude->setStateDescription($stateDescription);
                $etude->setStateID($stateID);
                $em->persist($etude);

                $em->flush();
            }
            
            
            return new Response('ok !');
         
    }
    
    
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction()
    {
        $etude = new Etude;
        
        $etude->setMandat(5);
        $etude->setNum($this->get('mgate.etude_manager')->getNouveauNumero($etude->getMandat()));
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user instanceof \mgate\UserBundle\Entity\User)
            $etude->setSuiveur($user->getPersonne());
        
        $form        = $this->createForm(new EtudeType(), $etude);
        $em = $this->getDoctrine()->getEntityManager();
        
        if($this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));

            if( $form->isValid() )
            {
                if(!$etude->isKnownProspect())
                {
                    $etude->setProspect($etude->getNewProspect());
                }
                
                $em->persist($etude);
                $em->flush();
           
                if($this->get('request')->get('ap'))
                {
                    return $this->redirect($this->generateUrl('mgateSuivi_ap_rediger', array('id' => $etude->getId())));
                }
                else
                {
                    return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())));
                }
            }
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Etude')->find($id); // Ligne qui posse problème

        if (!$entity)
            throw $this->createNotFoundException('Unable to find Etude entity.');
        
        //$deleteForm = $this->createDeleteForm($id);
        $formSuivi = $this->createForm(new SuiviType, $entity);
        return $this->render('mgateSuiviBundle:Etude:voir.html.twig', array(
            'etude'      => $entity,
            'formSuivi'      => $formSuivi->createView(),
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }

        $form = $this->createForm(new EtudeType, $etude);
        $deleteForm = $this->createDeleteForm($id);
        if($this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));

            if( $form->isValid() )
            {
                $em->persist($etude);
                $em->flush();

                return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:Etude:modifier.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
   
            if( ! $entity = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
                throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mgateSuivi_etude_homepage'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function suiviAction()
    {
        $em = $this->getDoctrine()->getManager();

        $MANDAT_MAX = 10;
        
        $etudesParMandat = array();
        for($i = 1; $i < $MANDAT_MAX; $i++)
            array_push ($etudesParMandat,$em->getRepository('mgateSuiviBundle:Etude')->findBy(array('mandat' => $i), array('id' => 'DESC')));
       
        return $this->render('mgateSuiviBundle:Etude:suiviEtudes.html.twig', array(
            'etudesParMandat' => $etudesParMandat,
        ));
         
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function suiviUpdateAction($id)
    {     
        $em = $this->getDoctrine()->getEntityManager();
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($id); // Ligne qui posse problème

        if (!$etude)
            throw $this->createNotFoundException('Unable to find Etude entity.');
        
        $formSuivi = $this->createForm(new SuiviType, $etude);
        if($this->get('request')->getMethod() == 'POST' )
        {
            $formSuivi->bind($this->get('request'));

            if( $formSuivi->isValid() )
            {
                $em->persist($etude);
                $em->flush();

               $return=array("responseCode"=>100, "msg"=>"ok");
            }
            else
                $return=array("responseCode"=>200, "msg"=>"Erreur:".$formSuivi->getErrorsAsString());
        }
            

        $return=json_encode($return);//jscon encode the array
        return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
     }
}
