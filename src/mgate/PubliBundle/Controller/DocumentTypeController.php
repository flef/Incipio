<?php
namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\DemoBundle\Entity\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use mgate\PubliBundle\Entity\DocumentType;

class DocumentTypeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('mgatePubliBundle:DocumentType:index.html.twig', array('name' => $name));
    }
    
    public function uploadAction()
    {
        $document = new DocumentType();
        $form = $this->createFormBuilder($document)
            ->add('name')
            ->add('file')
            ->getForm()
        ;

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($document);
                $em->flush();

                $this->redirect($this->generateUrl('/'));
            }
        }

        //return array('form' => $form->createView());
        return $this->render('mgatePubliBundle:DocumentType:index.html.twig', array('form' => $form->createView()));
    }
}
