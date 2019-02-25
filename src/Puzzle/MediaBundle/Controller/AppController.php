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
        
        $files = null;
        if (! empty($folder->getFiles())) {
            $list = $this->get('admin.util.doctrine_query_parameter')->formatForInClause($folder->getFiles());
            $dql   = "SELECT f FROM MediaBundle:File f WHERE f.id IN :list";
            $files = $em->createQuery($dql)
                        ->setParameter('list', $list)
                        ->getResults()
            ;
        }
        
    	return $this->render("AppBundle:Media:show_folder.html.twig", array(
    	    'folder' => $folder,
    	    'files' => $files,
    	));
    }
}
