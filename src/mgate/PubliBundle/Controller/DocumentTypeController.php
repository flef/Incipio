<?php
namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\DemoBundle\Entity\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PubliBundle\Entity\DocumentType;

class DocumentTypeController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePubliBundle:DocumentType')->findAll();

        return $this->render('mgatePubliBundle:DocumentType:index.html.twig', array(
            'docs' => $entities,
        ));
       
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function uploadAction()
    {
        $document = new DocumentType();
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

                $docs = $em->getRepository('mgatePubliBundle:DocumentType')->findBy(array('name' => $document->getName() )); // Ligne qui posse problème
                if ($docs) {
                    foreach ($docs as $doc) {
                        $em->remove($doc);
                    }
                }
                
                
                $em->persist($document);
                $em->flush();

                $this->redirect($this->generateUrl('mgate_publi_documenttype_index'));
            }
        }

        //return array('form' => $form->createView());
        return $this->render('mgatePubliBundle:DocumentType:upload.html.twig', array('form' => $form->createView()));
    }
}
