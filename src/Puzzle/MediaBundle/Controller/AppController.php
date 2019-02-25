<?php

namespace Puzzle\MediaBundle\Controller;

use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\MediaBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AppController extends Controller
{
    
    /**
     * List files
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFilesAction(Request $request) {
        return $this->render("AppBundle:Media:list_files.html.twig", array(
            'files' => $this->getDoctrine()->getRepository(File::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /**
     * Show File
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFileAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (! $file = $em->find(File::class, $id)) {
            $file = $em->getRepository(File::class)->findOneBy(['path' => $id]);
        }

    	return $this->render("AppBundle:Media:show_file.html.twig", array(
    	    'file' => $file
    	));
    }
    
    
    /***
     * List folders
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFoldersAction(Request $request) {
        return $this->render("AppBundle:Media:list_folders.html.twig", [
            'folders' => $this->getDoctrine()->getRepository(Folder::class)->findBy($_GET, ['createdAt' => 'DESC'])
        ]);
    }
    
    
    /***
     * Show Folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFolderAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (! $folder = $em->find(Folder::class, $id)) {
            $folder = $em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }

        $em    = $this->getDoctrine()->getManager();
        $dql   = "SELECT p FROM MediaBundle:File p WHERE p.folder = :folder";
        $query = $em->createQuery($dql)->setParameter('folder', $folder->getId());
        
        $paginator  = $this->get('knp_paginator');
        $files = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
        
    	return $this->render("AppBundle:Media:show_folder.html.twig", array(
    	    'folder' => $folder,
    	    'files' => $files,
    	));
    }
}
