<?php

namespace Puzzle\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\StaticBundle\Entity\Page;

/**
 * 
 * @author qwincy <cecenho55@gmail.com>
 *
 */
class AppController extends Controller
{
    /**
     * Show page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPageAction(Request $request, $slug){
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['slug' => $slug]);
        $name = $page->getName();
        $content = $page->getContent();
        $slug = $slug;
        
        $templateView = "default";
        if ($page->getTemplate() !== null){
            $templateView = $page->getTemplate()->getSlug();
        }
        
        // Default page view
    	return $this->render("AppBundle:Static:".$templateView.".html.twig", array(
    	    'page'         => $page,
    	    'name'         => $name,
    	    'content'      => $content,
    	    'slug'         => $slug
    	));
    }
}
