<?php

namespace mgate\TresoBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;


use \mgate\TresoBundle\Entity\NoteDeFrais as NoteDeFrais;
use \mgate\TresoBundle\Entity\NoteDeFraisDetail as NoteDeFraisDetail;
use mgate\TresoBundle\Form\NoteDeFraisType as NoteDeFraisType;

class NoteDeFraisController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $nfs = $em->getRepository('mgateTresoBundle:NoteDeFrais')->findAll();
        
        return $this->render('mgateTresoBundle:NoteDeFrais:index.html.twig', array('nfs' => $nfs));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function voirAction($id) {
        $em = $this->getDoctrine()->getManager();
        $nf = $em->getRepository('mgateTresoBundle:NoteDeFrais')->find($id);
        
        return $this->render('mgateTresoBundle:NoteDeFrais:voir.html.twig', array('nf' => $nf));
    }
    
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$nf= $em->getRepository('mgateTresoBundle:NoteDeFrais')->find($id)) {
            $nf = new NoteDeFrais;
            $now = new \DateTime("now");
            $nf->setDateSignature($now);           
        }

        $form = $this->createForm(new NoteDeFraisType, $nf);
        $detailsToRemove = $nf->getDetails()->toArray();

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));
            if ($form->isValid()) {
                if ($this->get('request')->get('add')) {
                    $detailNew = new NoteDeFraisDetail;
                    $nf->addDetail($detailNew);
                }
                // Suppression des detail à supprimer
                    //Recherche des details supprimés
                foreach ($nf->getDetails() as $detail){
                    $key = array_search($detail, $detailsToRemove);
                    if($key !== FALSE)
                        array_splice($detailsToRemove, $key, 1);
                }
                    //Supression de la BDD
                foreach ($detailsToRemove as $detail){
                    $em->remove($detail);
                }               

                $em->persist($nf); // persist $etude / $form->getData()
                $em->flush();
                
                $form = $this->createForm(new MembreType(), $nf);
            }
        }
        // TODO A modifier, l'ajout de poste dois se faire en js cf formation
        if ($this->get('request')->get('save'))
            return $this->redirect($this->generateUrl('mgateTreso_nf_voir', array('id' => $nf->getId())));
        
        return $this->render('mgateTresoBundle:NoteDeFrais:modifier.html.twig', array(
                    'form' => $form->createView(),
                ));
             
    }
}
