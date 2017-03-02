<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ScheduleController extends Controller
{
    /**
     * @Route("/Index")
     */
    public function IndexAction()
    {
        return $this->render('AppBundle:Schedule:index.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/Edit")
     */
    public function EditAction()
    {
        return $this->render('AppBundle:Schedule:edit.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/New")
     */
    public function NewAction()
    {
        return $this->render('AppBundle:Schedule:new.html.twig', array(
            // ...
        ));
    }

}
