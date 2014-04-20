<?php
namespace mgate\FormationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\FormationBundle\Form\FormationType;

use Symfony\Component\HttpFoundation\Request;

use mgate\FormationBundle\Entity\Formation;

class FormationController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('mgateFormationBundle:Formation')->findBy(array(), array('dateDebut' => 'DESC'));
      
        return $this->render('mgateFormationBundle:Gestion:index.html.twig', array(
            'formations' => $entities,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function listerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $formationsParMandat = $em->getRepository('mgateFormationBundle:Formation')->findAllByMandat();
              
        return $this->render('mgateFormationBundle:Formations:lister.html.twig', array(
            'formationsParMandat' => $formationsParMandat,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        if( ! $formation = $em->getRepository('mgate\FormationBundle\Entity\Formation')->find($id) )
            throw $this->createNotFoundException('La formation demandée n\'existe pas !');
      
        return $this->render('mgateFormationBundle:Formations:voir.html.twig', array(
            'formation' => $formation,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $formation = $em->getRepository('mgate\FormationBundle\Entity\Formation')->find($id) )
            $formation = new Formation;

      
        $form = $this->createForm(new FormationType, $formation);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($formation);
                $em->flush();
                
                $form = $this->createForm(new FormationType(), $formation);
            }
        }
        
        return $this->render('mgateFormationBundle:Gestion:modifier.html.twig', array(
            'form' => $form->createView(),
            'formation' => $formation,
        ));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function participationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $formationsParMandat = $em->getRepository('mgateFormationBundle:Formation')->findAllByMandat();
        
        $choices = array();
        foreach ($formationsParMandat as $key => $value){
            $choices[$key] = $key;
        }
        
        
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
                      ->add(
                          'mandat', 
                          'choice',
                          array(
                              'label'       =>'Présents aux formations du mandat ',
                              'choices'     => $choices,
                              'required'    =>true,
                              )
                      )->getForm();
        
        if ($request->isMethod('POST'))
        { 
            $form->bind($request);
            $data = $form->getData();
            $mandat = $data['mandat'];
            $formations = array_key_exists($mandat, $formationsParMandat) ? $formationsParMandat[$mandat] : array();
        }else{
            $formations = count($formationsParMandat) ? reset($formationsParMandat) : array();
        }

        
        $presents = array();
        
        foreach ($formations as $formation){
            foreach($formation->getMembresPresents() as $present){
                $id = $present->getPrenomNom();
                if(array_key_exists($id, $presents)){
                    $presents[$id][] = $formation->getId();
                }else
                     $presents[$id] = array($formation->getId());
            }
        }
        
              
        return $this->render('mgateFormationBundle:Gestion:participation.html.twig', array(
            'form' => $form->createView(),
            'formations' => $formations,
            'presents' => $presents,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        if( ! $formation = $em->getRepository('mgate\FormationBundle\Entity\Formation')->find($id) )
            throw $this->createNotFoundException('La formation demandée n\'existe pas !');

        $em->remove($formation);                
        $em->flush();
        return $this->redirect($this->generateUrl('mgate_formations_lister', array()));
      
    }
}