<?php 

namespace Puzzle\MediaBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\MediaBundle\Event\FolderEvent;
use Puzzle\MediaBundle\Service\FileManager;
use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\AdminBundle\Event\AdminInstallationEvent;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class FolderListener
{
	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @var FileManager
	 */
	private $fm;
	
	public function __construct(EntityManager $em, FileManager $fm){
		$this->em = $em;
		$this->fm = $fm;
	}
	
	public function onAdminInstalling(AdminInstallationEvent $event) {
	    $folder = $this->em->getRepository("MediaBundle:Folder")->findOneBy([
	        'appName' => Folder::ROOT_APP_NAME,
	        'name' => Folder::ROOT_NAME
	    ]);
	    
	    if ($folder === null) {
	        $folder = new Folder();
	        $folder->createDefault();
	        
	        $this->em->persist($folder);
	        $this->em->flush($folder);
	    }
	    
	    $event->notifySuccess('Creating default folder successfull');
	}
	
	/**
	 * Folder created on disk
	 * 
	 * @param FolderEvent $event
	 */
	public function onCreate(FolderEvent $event)
	{
	    $folder = $event->getFolder();
		
		if (file_exists($folder->getAbsolutePath()) === false) {
		    return mkdir($folder->getAbsolutePath(), 0777, true);
		}
		
		return true;
	}
	
	/**
	 * Rename folder on disk
	 *
	 * @param FolderEvent $event
	 */
	public function onUpdate(FolderEvent $event) {
	    $folder = $event->getFolder();
	    $data = $event->getData();
	    if (file_exists($folder->getAbsolutePath()) === false) {
	        return rename($data['oldAbsolutePath'], $folder->getAbsolutePath());
	    }
	    
	    return true;
	}
	
	/**
	 * Remove folder on disk
	 *
	 * @param FolderEvent $event
	 */
	public function onRemove(FolderEvent $event)
	{
	    $folder = $event->getFolder();
	    $folderPath = $folder->getAbsolutePath();
	    if (file_exists($folderPath) === true) {
	        $this->fm->removeDirectory($folderPath);
	    }
	    
	    return true;
	}
	
	/**
	 * Add files to folder
	 *
	 * @param FolderEvent $event
	 */
	public function onAddFiles(FolderEvent $event)
	{
	    $folder = $event->getFolder();
	    $data = $event->getData();
	    if ($folder->getFiles() !== null) {
	        $er = $this->em->getRepository("MediaBundle:File");
	        foreach ($folder->getFiles() as $fileId){
	            /** @var File $file*/
	            $file = $er->find($fileId);
	            if ($file !== null && file_exists($file->getAbsolutePath())) {
	                if (isset($data['preserve_files']) && $data['preserve_files'] === false) {
	                    $this->fm->moveFile($file, $folder);
	                }else {
	                    $this->fm->copyFile($file, $folder);
	                }
	            }
	        }
	    }
	    
	    return true;
	}
	
	/**
	 * Empty folder
	 *
	 * @param FolderEvent $event
	 */
	public function onRemoveFiles(FolderEvent $event)
	{
	    $folder = $event->getFolder();
	    
	    if ($folder->getFiles() === null) {
	        $er = $this->em->getRepository("MediaBundle:File");
	        foreach ($folder->getFiles() as $fileId){
	            /** @var File $file*/
	            $file = $er->find($fileId);
	            if ($file !== null && file_exists($file->getAbsolutePath())) {
	                unlink($file->getAbsolutePath(), $folder->getPath());
	            }
	        }
	    }
	    
	    return true;
	}
}

?>
