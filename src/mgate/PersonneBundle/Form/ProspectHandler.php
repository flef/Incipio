<?php

namespace mgate\PersonneBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use mgate\PersonneBundle\Entity\Prospect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\CommentBundle\Entity\ThreadManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;

class ProspectHandler extends Controller
{
    protected $form;
    protected $request;
    protected $em;
    protected $tm;
    protected $router;

    public function __construct(Form $form, Request $request, EntityManager $em, ThreadManager $tm, Router $router)
    {
        $this->form    = $form;
        $this->request = $request;
        $this->em      = $em;
        $this->tm      = $tm;
        $this->router  = $router;
    }

    public function process()
    {
        if( $this->request->getMethod() == 'POST' )
        {
            $this->form->bindRequest($this->request);

            if( $this->form->isValid() )
            {
                $this->onSuccess($this->form->getData());

                return true;
            }
        }

        return false;
    }

    public function onSuccess(Prospect $pro)
    {
        $this->em->persist($pro);    
        $this->em->flush();
        
        //la y avait du code pour les thread
    }
}

