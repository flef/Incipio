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

define("STATE_ID_EN_NEGOCIATION",1);
define("STATE_ID_EN_COURS", 2);
define("STATE_ID_EN_PAUSE",3);
define("STATE_ID_TERMINEE",4);
define("STATE_ID_AVORTEE",5);



class EtudeController extends Controller
{
    
    /*
     * 
     * 
     *         //Confidentialité : Visibilité CA, Suiveur
        $userToken = $this->container->get('security.context');
        $user = $userToken->getToken()->getUser()->getPersonne();
        
        if($etude->getConfidentiel() && !$userToken->isGranted('ROLE_CA') && $user->getId() != $etude->getSuiveur()->getId())
                throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
        ///
     * 
     */
 
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page)
    {
        $MANDAT_MAX = $this->get('mgate.etude_manager')->getMaxMandat();         
        
        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.context')->getToken()->getUser()->getPersonne();
 
        //Etudes En Négociation : stateID = 1
        $etudesEnNegociation = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_NEGOCIATION), array('mandat'=> 'DESC', 'num'=> 'DESC'));
        
        //Etudes En Cours : stateID = 2
        $etudesEnCours = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_COURS), array('mandat'=> 'DESC', 'num'=> 'DESC'));

        //Etudes en pause : stateID = 3
        $etudesEnPause = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_PAUSE),array('mandat'=> 'DESC', 'num' => 'DESC'));

        //Etudes Terminees : stateID = 4
        $etudesTermineesParMandat = array();
        for($i = 1; $i <= $MANDAT_MAX; $i++)
            array_push ($etudesTermineesParMandat,$em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_TERMINEE, 'mandat' => $i), array('num' => 'DESC')));
           
        
        //Etudes Avortees : stateID = 5
        $etudesAvorteesParMandat = array();
        for($i = 1; $i <= $MANDAT_MAX; $i++)
            array_push ($etudesAvorteesParMandat,$em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_AVORTEE,'mandat' => $i),array('num' => 'DESC')));
        
        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudesEnNegociation' => $etudesEnNegociation,
            'etudesEnCours' => $etudesEnCours,
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
        
        $etude->setMandat($this->get('mgate.etude_manager')->getMaxMandat());
        $etude->setNum($this->get('mgate.etude_manager')->getNouveauNumero());
        
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
     
        $etude = new \mgate\SuiviBundle\Entity\Etude;
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($id); // Ligne qui posse problème

        if (!$etude)
            throw $this->createNotFoundException('Unable to find Etude entity.');
        
        $chartManager = $this->get('mgate.chart_manager');
        $ob=$chartManager->getGantt($etude, "suivi");

       
        //$deleteForm = $this->createDeleteForm($id);
        $formSuivi = $this->createForm(new SuiviType, $etude);
        return $this->render('mgateSuiviBundle:Etude:voir.html.twig', array(
            'etude'      => $etude,
            'formSuivi'      => $formSuivi->createView(),
            'chart' => $ob,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        
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
            
            foreach ($entity->getMissions() as $mission) {
                foreach ($mission->getPhaseMission() as $PhaseMission) {
                    $em->remove($PhaseMission); // suppression répartition JEH
                }
                $em->remove($mission); 
            }
            foreach ($entity->getPhases() as $phase) {
                $em->remove($phase); //suppression des phases
            }            

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
            array_push ($etudesParMandat,$em->getRepository('mgateSuiviBundle:Etude')->findBy(array('mandat' => $i), array('num' => 'DESC')));
       
        //WARN
        /* Création d'un form personalisé sans classes (Symfony Forms without Classes)
         * 
         * Le problème qui se pose est de savoir si les données reçues sont bien destinées aux bonnes études
         * Si quelqu'un ajoute une étude ou supprime une étude pendant la soumission de se formulaire, c'est la cata
         * tout se décale de 1 étude !!
         * J'ai corrigé ce bug en cas d'ajout d'une étude. Les changements sont bien sauvegardés !!
         * Mais cette page doit être rechargée et elle l'est automatiquement. (Si js est activé !)
         * bref rien de bien fracassant. Solution qui se doit d'être temporaire bien que fonctionnelle !
         * Cependant en cas de suppression d'une étude, chose qui n'arrive pas tous les jours, les données seront perdues !!
         * Perdues Perdues !!!
         */
        $etudesEnCours = array();
        
        $NbrEtudes = 0;
        foreach ($etudesParMandat as $etudesInMandat)
            $NbrEtudes += count ($etudesInMandat);
        
        $form = $this->createFormBuilder();
        
        $id = 0;
        foreach(array_reverse($etudesParMandat) as $etudesInMandat){
            foreach ($etudesInMandat as $etude)                
            {
                $form = $form->add((string) (2*$id), 'hidden', array('label' => 'refEtude', 'data' => $this->get('mgate.etude_manager')->getRefEtude($etude)))
                             ->add((string) (2*$id+1), 'textarea', array('label' => $this->get('mgate.etude_manager')->getRefEtude($etude), 'required' => false, 'data' => $etude->getStateDescription() ));
                $id++;
                if($etude->getStateID() == STATE_ID_EN_COURS)
                array_push($etudesEnCours, $etude);
            }
        }  
        $form = $form->getForm();
        
        if($this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));

            $data = $form->getData();
            
            $id = 0;
            foreach(array_reverse($etudesParMandat) as $etudesInMandat){
                foreach ($etudesInMandat as $etude)                
                {
                    if($data[2*$id] == $this->get('mgate.etude_manager')->getRefEtude($etude)){
                        if($data[2*$id] != $etude->getStateDescription()){
                            $etude->setStateDescription($data[2*$id+1]);
                            $em->persist($etude);
                            $id++;
                        }     
                    }
                    else{
                        echo '<script>location.reload();</script>';
                    }
                }
            }  
            $em->flush();
         }
        
        
        $chartManager = $this->get('mgate.chart_manager');
        $ob=$chartManager->getGanttSuivi($etudesEnCours);        
        
        
        
        return $this->render('mgateSuiviBundle:Etude:suiviEtudes.html.twig', array(
            'etudesParMandat' => $etudesParMandat,
            'form' => $form->createView(),
            'chart' => $ob,
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
     
     private function searchArrayID(array $etudes, Etude $etude){
            $i = 0;
            foreach($etudes as $e){
                if($e->getId() == $etude->getId())
                    return $i;
                else
                    $i++;
            }
            return -1;
        }
        
        private function constructArrayAssoc(array $etudes){
            $etudesAssoc = array();
            foreach($etudes as $e){
                $etudesAssoc[$e->getId()] = $this->get('mgate.etude_manager')->getRefEtude($e) . " - " . $e->getNom();
            }
            return $etudesAssoc;
        }


        /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function vuCAAction($id){
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($id);
        
        if (!$etude)
            throw $this->createNotFoundException('Unable to find Etude entity.');
        
        //Etudes En Négociation : stateID = 1
        $etudesEnNegociation = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_NEGOCIATION), array('mandat'=> 'ASC', 'num'=> 'ASC'));
        
        //Etudes En Cours : stateID = 2
        $etudesEnCours = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_COURS), array('mandat'=> 'ASC', 'num'=> 'ASC'));
        
        $etudes = array_merge($etudesEnNegociation,$etudesEnCours);

        $id = $this->searchArrayID($etudes,$etude);
        
        if ($id == -1)
            throw $this->createNotFoundException('Etude incorrecte');
        
        
        $nId = $id+1;
        $pId = $id-1;
        if($nId >= count($etudes)) $nId--;
        if($pId < 0 ) $pId = 0; 
        
        $nextID = $etudes[$nId]->getId();
        $prevID = $etudes[$pId]->getId();
        
        $chartManager = $this->get('mgate.chart_manager');
        $ob=$chartManager->getGantt($etude, "suivi");

       
        return $this->render('mgateSuiviBundle:Etude:vuCA.html.twig', array(
            'etude' => $etude,
            'chart' => $ob,
            'nextID' => $nextID,
            'prevID' => $prevID,
           
            'listEtudesNegociate' => $this->constructArrayAssoc($etudesEnNegociation),
            'listEtudesCurrent' => $this->constructArrayAssoc($etudesEnCours),
        ));
        

    }
    
            
     
}
