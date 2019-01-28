<?php

namespace Puzzle\MediaBundle\Controller;

use Puzzle\MediaBundle\Entity\File;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Form\Type\FolderCreateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Puzzle\MediaBundle\Event\FolderEvent;
use Puzzle\MediaBundle\Form\Type\FolderUpdateType;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AdminController extends Controller
{
	/***
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listFilesAction(Request $request){
	    $em = $this->getDoctrine()->getManager();
	    $type = $request->get('type');
	    $filters = $joins = null;
	    
	    $criteria = [];
	    if ($request->get('search')) {
	        $criteria[] = ['name', '%'.$request->get('search').'%', 'LIKE'];
	    }
	    
	    switch ($type) {
	        case "picture":
	            $filters .= "*.(". MediaUtil::supportedPictureExtensions().")";
	            $criteria[] = ['p.id', null, 'IS NOT NULL']; // fieldName, Value, Operator
	            $joins = ["picture" => "p"]; // Associated Name, Alias
	            break;
	        case "audio":
	            $filters .= "*.(".MediaUtil::supportedAudioExtensions().")";
	            $criteria[] = ['a.id', null, 'IS NOT NULL'];
	            $joins = ["audio" => "a"];
	            break;
	        case "video":
	            $filters .= "*.(".MediaUtil::supportedVideoExtensions().")";
	            $criteria[] = ['v.id', null, 'IS NOT NULL'];
	            $joins = ["video" => "v"];
	            break;
	        case "document":
	            $filters .= "*.(".MediaUtil::supportedDocumentExtensions().")";
	            $criteria[] = ['d.id', null, 'IS NOT NULL'];
	            $joins = ["document" => "d"];
	            break;
	        case "other":
	            $criteria[] = ['p.id', null, 'IS NOT NULL'];
	            $criteria[] = ['a.id', null, 'IS NOT NULL'];
	            $criteria[] = ['v.id', null, 'IS NOT NULL'];
	            $criteria[] = ['d.id', null, 'IS NOT NULL'];
	            $joins = ["picture" => "p", "document" => "d", "audio" => "a", "video" => "v"];
	            $filters = "*";
	            break;
	        default:
	            $filters = "*";
	            break;
	    }
	    
	    $files = $em->getRepository(File::class)->customFindBy(null,$joins, $criteria, ['createdAt' => 'DESC']);
	    $folders = $em->getRepository(Folder::class)->customFindBy(
	        null, null, [['appName', 'media']], ['createdAt' => 'DESC']
	        );
	    
	    if ($request->get('target') == 'modal') {
	        return $this->render("AdminBundle:Media:list_files_in_modal.html.twig", array(
	            'type' => $type,
	            'filters' => $filters,
	            'files' => $files,
	            'enableMultipleSelect' => $request->get('multiple_select') ? true: false,
	            'context' => $request->get('context')
	        ));
	    }
	    
	    return $this->render("AdminBundle:Media:list_files.html.twig",[
	        'files' => $files,
	        'type' => $type,
	        'folders' => $folders,
	        'filters' => $filters
	    ]);
	}
	
	/***
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function browseFilesAction(Request $request){
	    $em = $this->getDoctrine()->getManager();
	    $type = $request->get('type');
	    $filters = $joins = null;
	    
	    $criteria = [];
	    if ($request->get('search')) {
	        $criteria[] = ['name', '%'.$request->get('search').'%', 'LIKE'];
	    }
	    
	    switch ($type){
	        case "picture":
	            $filters .= "*.(". MediaUtil::supportedPictureExtensions().")";
	            $criteria[] = ['p.id', null, 'IS NOT NULL']; // fieldName, Value, Operator
	            $joins = ["picture" => "p"]; // Associated Name, Alias
	            break;
	        case "audio":
	            $filters .= "*.(".MediaUtil::supportedAudioExtensions().")";
	            $criteria[] = ['a.id', null, 'IS NOT NULL'];
	            $joins = ["audio" => "a"];
	            break;
	        case "video":
	            $filters .= "*.(".MediaUtil::supportedVideoExtensions().")";
	            $criteria[] = ['v.id', null, 'IS NOT NULL'];
	            $joins = ["video" => "v"];
	            break;
	        case "document":
	            $filters .= "*.(".MediaUtil::supportedDocumentExtensions().")";
	            $criteria[] = ['d.id', null, 'IS NOT NULL'];
	            $joins = ["document" => "d"];
	            break;
	        case "other":
	            $criteria[] = ['p.id', null, 'IS NOT NULL'];
	            $criteria[] = ['a.id', null, 'IS NOT NULL'];
	            $criteria[] = ['v.id', null, 'IS NOT NULL'];
	            $criteria[] = ['d.id', null, 'IS NOT NULL'];
	            $joins = ["picture" => "p", "document" => "d", "audio" => "a", "video" => "v"];
	            $filters = "*";
	            break;
	        default:
	            $filters = "*";
	            break;
	    }
	    
	    $files = $em->getRepository(File::class)->customFindBy(null,$joins, $criteria, ['createdAt' => 'DESC']);
	    
	    return $this->render("AdminBundle:Media:browse_files.html.twig",[
	        'type' => $type,
	        'filters' => $filters,
	        'files' => $files,
	        'enableMultipleSelect' => $request->get('multiple_select') ? true: false,
	        'context' => $request->get('context')
	    ]);
	}
	
	/**
	 * Embed file
	 * 
	 * @param Request $request
	 * @param mixed $formData
	 * @param mixed $data
	 * @param mixed $type
	 * @param mixed $context
	 * @param boolean $multiple
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function embedFileAction(Request $request, $formData, $data, $type, $context, $multiple = false) {
	    $filters = '*';
	    $accept = '*';
	    
	    switch ($type){
	        case "picture":
	            $filters = "*.(". MediaUtil::supportedPictureExtensions().")";
	            $accept = 'image/*';
	            break;
	        case "audio":
	            $filters = "*.(".MediaUtil::supportedAudioExtensions().")";
	            $accept = 'audio/*';
	            break;
	        case "video":
	            $filters = "*.(".MediaUtil::supportedVideoExtensions().")";
	            $accept = 'video/*';
	            break;
	        case "document":
	            $filters = "*.(".MediaUtil::supportedDocumentExtensions().")";
	            $accept = 'document/*';
	            break;
	        default:
	            break;
	    }
	    
	    return $this->render('AdminBundle:Media:embed_file.html.twig', [
	        'filters' => $filters,
	        'type' => $type,
	        'accept' => $accept,
	        'data' => $data,
	        'formData' => $formData,
	        'multiple' => $multiple,
	        'context' => $context
	        
	    ]);
	}
	
	public function showFileAction(Request $request, File $file) {
	    return $this->render('AdminBundle:Media:show_file.html.twig', ['file' => $file]);
	}
	
	
	public function addFilesFormAction(Request $request) {
	    $folderId = $request->query->get('folder');
	    $folder = $folderId ? $this->getDoctrine()->getRepository(Folder::class)->find($folderId) : null;
	    
	    return $this->render('AdminBundle:Media:add_files', ['folder' => $folder]);
	}
	
	/**
	 * Upload Media From Another App
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function uploadFileAction(Request $request){
	    $folder = $this->get('media.file_manager')->createFolder($request->query->get('context'), $this->getUser());
	    $media = $this->get('media.upload_manager')->prepareUpload($_FILES, $folder, $this->getUser());
	    
	    if (count($media) == 1) {
	        $data = ['url' => $media[0]->getPath(), 'id' => $media[0]->getId()];
	    }else {
	        $urls = $ids = [];
	        foreach ($media as $medium){
	            $urls[] = $medium->getPath();
	            $ids[] = $medium->getId();
	        }
	        
	        $data = ['url' => implode(',', $urls), 'id' => implode(',', $ids)];
	    }
	    
	    return new JsonResponse($data);
	}
	
	/**
	 * Add file
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function addFileAction(Request $request) {
        $data = $request->request->all();
        
        $em = $this->getDoctrine()->getManager();
        
        if ($data['source'] === "local") { // local
            $file = $em->getRepository(File::class)->findOneBy(['path' => $data['path']]);
        }else { // Remote
            if (isset($data['uploadable']) && $data['uploadable'] === "checked"){
                $file = $this->get('media.upload_manager')->uploadFormUrl($data['path'], $this->getUser());
            }else {
                
                if (isset($data['folder']) && $data['folder'] != null) {
                    $folder = $em->getRepository(Folder::class)->find($data['folder']);
                }else {
                    $folder = $this->get('media.file_manager')->createFolder(Folder::ROOT_APP_NAME, $this->getUser());
                }
                
                $file = new File();
                $file->setName($data['name']);
                $file->setPath($data['path']);
                
                $em->persist($file);
                $folder->addFile($file->getId());
            }
            
            $file->setCaption($data['caption']);
            $em->flush();
        }
        
        if (isset($data['folder']) && $data['folder'] != null) {
            return $this->redirect($this->generateUrl('admin_media_folder_show', ['id' => $data['folder']]));
        }
        
        return $this->redirect($this->generateUrl('admin_media_file_list'));
    }

    /***
     * Delete File
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFileAction(Request $request, File $file) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($file);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(['status' => true]);
        }
        
        return $this->redirectToRoute('admin_media_file_list');
    }
    
    
    /***
     * List folders
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFoldersAction(Request $request) {
        $er = $this->getDoctrine()->getRepository(Folder::class);
        
        $folderDefault = $er->findOneBy(['appName' => Folder::ROOT_APP_NAME, 'name' => Folder::ROOT_NAME]);
        $folders = $er->customFindBy(
            null, null, [['parent', null, 'IS NULL'], ['appName', 'media', '!=']]
        );
        
        return $this->render("AdminBundle:Media:list_folders.html.twig", array(
            'folders' => $folders,
            'folderDefault' => $folderDefault
        ));
    }
    
    /***
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function browseFoldersAction(Request $request) {
        $er = $this->getDoctrine()->getRepository(Folder::class);
        
        if ($request->query->get('folder') === null || ! $parent = $er->find($request->query->get('folder'))) {
            $parent = $er->findOneBy(['appName' => Folder::ROOT_APP_NAME, 'name' => Folder::ROOT_NAME]);
        }
        
        $folders = $er->customFindBy(
            null, null, [['parent', $parent->getId()], ['appName', 'media']]
        );
        
        return $this->render("AdminBundle:Media:browse_folders.html.twig", array(
            'operation' => $request->query->get('operation'),
            'parent' => $parent,
            'folders' => $folders
        ));
    }
    
    
    /***
     * Show Folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFolderAction(Request $request, Folder $folder) {
        $em = $this->getDoctrine()->getManager();
        
        $criteria = [];
        
        if ($request->query->get('search')) {
            $criteria[] = ['name', '%'.$request->query->get('search').'%', 'LIKE'];
        }
        
        $customCriteria = $criteria;
        $customCriteria[] = ['parent', $folder->getId()];
        
        $childs = $em->getRepository(Folder::class)->customFindBy(null, null, $customCriteria);
        unset($customCriteria);
        
        $files = $list = null;
        foreach ($folder->getFiles() as $key => $file) {
            if ($key == 0){
                $list = "'".$file."'";
            }else {
                $list .= ",'".$file."'";
            }
        }
        
        $customCriteria = $criteria;
        $customCriteria[] = ['id', null, 'IN ('.$list.')'];
        if ($list !== null) {
            $files = $em->getRepository(File::class)->customFindBy(null, null, $customCriteria);
        }
        
        unset($customCriteria);
        unset($criteria);
        unset($list);
        
        return $this->render("AdminBundle:Media:show_folder.html.twig", array(
            'folder' => $folder,
            'childs' => $childs,
            'files' => $files
        ));
    }
    
    
    /***
     * Create Folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createFolderAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        $parentId = $request->query->get('parent');
        $parent = $em->getRepository(Folder::class)->find($parentId);
        
        $folder = new Folder();
        $folder->setParent($parent);
        
        $form = $this->createForm(FolderCreateType::class, $folder, [
            'method' => 'POST',
            'action' => $parent ?
                        $this->generateUrl('admin_media_folder_create', ['parent' => $parentId]) :
                        $this->generateUrl('admin_media_folder_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $folder->setAppName(Folder::ROOT_APP_NAME);
            $folder->setAllowedExtensions($folder->getAllowedExtensions() !== null ? explode(',', $folder->getAllowedExtensions()) : null);
            
            if ($folder->getParent() === null) {
                $parent = new Folder();
                $parent->setName(Folder::ROOT_NAME);
                $parent->setAppName(Folder::ROOT_APP_NAME);
                
                $em->persist($parent);
            }
            
            $em->persist($folder);
            $em->flush();
            
            $this->get('event_dispatcher')->dispatch(MediaEvents::CREATE_FOLDER, new FolderEvent($folder));
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            if ($folder->getParent() !== null) {
                return $this->redirectToRoute('admin_media_folder_show', array('id' => $folder->getParent()->getId()));
            }
            
            return $this->redirectToRoute('admin_media_folder_list');
        }
        
        return $this->render("AdminBundle:Media:create_folder.html.twig", [
            'form' => $form->createView()
        ]);
    }
    
    /***
     * Update folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateFolderAction(Request $request, Folder $folder) {
        $oldAbsolutePath = $folder->getAbsolutePath();
        $form = $this->createForm(FolderUpdateType::class, $folder, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_media_folder_update', ['id' => $folder->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $folder->setAllowedExtensions($folder->getAllowedExtensions() !== null ? explode(',', $folder->getAllowedExtensions()) : null);
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            if ($oldAbsolutePath != $folder->getAbsolutePath()) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::RENAME_FOLDER, new FolderEvent($folder, ['oldAbsolutePath' => $oldAbsolutePath]));
            }
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_media_folder_show', array('id' => $folder->getId()));
        }
        
        return $this->render("AdminBundle:Media:update_folder.html.twig", [
            'folder' => $folder,
            'form' => $form->createView()
        ]);
    }
    
    /**
     *
     * Update Folder by adding file
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateFolderByAddingFilesAction(Request $request, Folder $folder) {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        
        if (isset($data['files_to_add'])) {
            $filesTaAdd = is_string($data['files_to_add']) ? explode(',', $data['files_to_add']) : $data['files_to_add'];
            
            foreach ($filesTaAdd as $fileTaAdd) {
                $folder->addFile($fileTaAdd);
            }
        }
        
        $folder->setLastEditor($this->getUser()->getId());
        $em->flush();
        
        if (isset($data['operation']) && $data['operation'] == "move") {
            $folderEvent = new FolderEvent($folder, ['preserve_files' => false]);
        }else {
            $folderEvent = new FolderEvent($folder);
        }
        
        $this->get('event_dispatcher')->dispatch(MediaEvents::ADD_FILES_TO_FOLDER, $folderEvent);
        
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => true]);
        }
        
        return $this->redirectToRoute('admin_media_folder_show', array('id' => $folder->getId()));
    }
    
    /**
     *
     * Update Folder by adding file
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateFolderByRemovingFilesAction(Request $request, Folder $folder) {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        
        if (isset($data['ids']) === true) {
            $list = explode(',', $data['ids']);
            foreach ($list as $item){
                $file = $em->getRepository(File::class)->find($item);
                $folder->removeFile($file->getId());
                $this->get('event_dispatcher')->dispatch(MediaEvents::REMOVE_FILE, new FileEvent([
                    'absolutePath' => $file->getAbsolutePath()
                ]));
                
                $em->remove($file);
            }
        }
        
        $em->flush();
        
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => true]);
        }
        
        return $this->redirectToRoute('admin_media_folder_show', array('id' => $folder->getId()));
    }
    
    
    /***
     * Delete Folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFolderAction(Request $request, Folder $folder) {
        if ($folder->getParent()) {
            $route = $this->redirectToRoute('admin_media_folder_show', array('id' => $folder->getParent()->getId()));
        }else {
            $route = $this->redirectToRoute('admin_media_folder_list');
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($folder);
        
        $this->get('event_dispatcher')->dispatch(MediaEvents::REMOVE_FOLDER, new FolderEvent($folder));
        $em->flush();
        
        return $route;
    }
    
    
    /**
     * Compress folder
     * 
     * @param Request $request
     * @param Folder $folder
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function compressFolderAction(Request $request, Folder $folder) {
//         $source = $folder->getAbsolutePath();
//         $zip = $folder->getAbsolutePath().'.zip';
//         exec('zip '. $zip ." ". $source);
        $dest = $this->get('media.file_manager')->zipDir($folder->getAbsolutePath());
        if ($dest === false) {
            return new JsonResponse(['status' => false]);
        }
        
        return new JsonResponse(['status' => true, 'target' => $folder->getPath().'.zip']);
    }
}
