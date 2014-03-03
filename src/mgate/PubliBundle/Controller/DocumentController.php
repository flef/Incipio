<?php
namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PubliBundle\Entity\Document;

class DocumentController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
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
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function uploadAction($deleteIfExist = false)
    {
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('name')
            ->add('file')
            ->getForm();
        
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid())
            {
                $document->setName(strtoupper($document->getName()));
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
                
                $this->redirect($this->generateUrl('mgate_publi_documenttype_index'));
            }
        }

        //return array('form' => $form->createView());
        return $this->render('mgatePubliBundle:Document:upload.html.twig', array('form' => $form->createView()));
    }
}
