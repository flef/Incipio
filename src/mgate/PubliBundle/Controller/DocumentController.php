<?php
namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PubliBundle\Entity\Document;
use mgate\PubliBundle\Form\DocumentType;

class DocumentController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePubliBundle:Document')->findAll();
      
        return $this->render('mgatePubliBundle:Document:index.html.twig', array(
            'docs' => $entities,
        ));
       
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    public function uploadEtudeAction($id){
        
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    public function uploadEtudiantAction($id){
        
    }   
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function uploadFormationAction($id){

    } 
        
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function uploadDoctypeAction(){
        if(!$this->upload(true))// Si tout est ok
            return $this->redirect($this->generateUrl('mgate_publi_documenttype_index'));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        if (!$doc= $em->getRepository('mgatePubliBundle:Document')->find($id))
            throw $this->createNotFoundException('Le Document n\'existe pas !');

        $em->remove($doc);
        $em->flush();        
        
        return $this->redirect($this->generateUrl('mgate_publi_documenttype_index'));  
    }

    private function upload($deleteIfExist = false, $options = array())
    {
        $document = new Document();

        $form = $this->createForm(new DocumentType, $document, $options);
       
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $user = $this->get('security.context')->getToken()->getUser();
                $personne = $user->getPersonne();
        
                $document->setName(strtoupper($document->getName()));
                $document->setAuthor($personne);
                $em = $this->getDoctrine()->getManager();
                $em->persist($document);
                $em->flush();

                
                if($deleteIfExist){
                    $docs = $em->getRepository('mgatePubliBundle:Document')->findBy(array('name' => $document->getName() ));
                    if ($docs) {
                        foreach ($docs as $doc) {
                            $em->remove($doc);
                        }
                    }
                }
                
                return false;
            }
        }
        return $this->render('mgatePubliBundle:Document:upload.html.twig', array('form' => $form->createView()));
    }
}
