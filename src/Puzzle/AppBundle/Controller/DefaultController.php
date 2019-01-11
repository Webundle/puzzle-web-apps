<?php

namespace Puzzle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Puzzle\StaticBundle\Entity\Page;

class DefaultController extends Controller
{
    public function indexAction() {
        return $this->render('AppBundle:Default:index.html.twig');
    }
    
    public function aboutUsAction() {
        $page = $this->getDoctrine()->getRepository(Page::class)->findOneBy(['slug' => 'notre-vision']);
        return $this->render('AppBundle:Default:about_us.html.twig', ['page' => $page]);
    }
    
    public function showOurValuesAction() {
        $page = $this->getDoctrine()->getRepository(Page::class)->findOneBy(['slug' => 'our-values']);
        return $this->render('AppBundle:Default:our_values.html.twig', ['page' => $page]);
    }
    
    public function showOurExpertiseAction() {
        $page = $this->getDoctrine()->getRepository(Page::class)->findOneBy(['slug' => 'notre-expertise']);
        return $this->render('AppBundle:Default:our_expertise.html.twig', ['page' => $page]);
    }
    
    public function showOurTeamAction() {
        $page = $this->getDoctrine()->getRepository(Page::class)->findOneBy(['slug' => 'our-team']);
        return $this->render('AppBundle:Default:our_team.html.twig', ['page' => $page]);
    }
    
    public function contactAction() {
        return $this->render('AppBundle:Default:contact.html.twig');
    }
}
