<?php

namespace Puzzle\SchedulingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SchedulingBundle:Default:index.html.twig', array('name' => $name));
    }
}
