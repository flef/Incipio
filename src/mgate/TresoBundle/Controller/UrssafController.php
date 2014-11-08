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


namespace mgate\TresoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UrssafController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('date', 'genemu_jquerydate', array('label'=>'Nombre de développeur au :', 'required'=>true, 'widget'=>'single_text', 'data'=>date_create(),'format' => 'dd/MM/yyyy',))
            ->getForm();

        $RMs =  array();
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
            $data = $form->getData();
            
            //$RMs = $em->getRepository('mgateSuiviBundle:Mission')->findBy(array('$debutOm' => 1));
            
            $qb = $em->createQueryBuilder();
            $qb->select('m')
                ->from('mgateSuiviBundle:Mission', 'm')
                ->where('m.debutOm <= :date')
                ->orderBy('m.finOm', 'DESC')
                //->andWhere('m.finOm >= :date')
                ->setParameters(array('date' => $data["date"]));

            $RMs = $qb->getQuery()->getResult();

        }

        
        
        return $this->render('mgateTresoBundle:Urssaf:index.html.twig', array('form' => $form->createView(), 'RMs' => $RMs));
    }
}
