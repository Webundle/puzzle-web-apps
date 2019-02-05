<?php 

namespace Puzzle\MediaBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\MediaBundle\Event\FolderEvent;
use Puzzle\MediaBundle\Service\FileManager;
use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\AdminBundle\Event\AdminInstallationEvent;
use Puzzle\MediaBundle\Entity\File;
use Doctrine\ORM\Event\LifecycleEventArgs;

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
	
	protected $baseDir;
	
	public function __construct(EntityManager $em, FileManager $fm, $baseDir){
		$this->em = $em;
		$this->fm = $fm;
		$this->baseDir = $baseDir;
	}
	
	public function onAdminInstalling(AdminInstallationEvent $event) {
	    $folder = $this->em->getRepository(Folder::class)->findOneBy([
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
	 * Folder class load
	 * 
	 * @param LifecycleEventArgs $args
	 */
	public function postLoad(LifecycleEventArgs $args) {
	    $folder = $args->getEntity();
	    
	    if (!$folder instanceof Folder) {
	        return;
	    }
	    
	    $folder->setBaseDir($this->baseDir);
	}
	
	/**
	 * Folder created on disk
	 * 
	 * @param FolderEvent $event
	 */
	public function onCreate(FolderEvent $event)
	{
	    $folder = $event->getFolder();
	    $folder->setBaseDir($this->baseDir);
	    
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
	    $folder->setBaseDir($this->baseDir);
	    
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
	    $folder->setBaseDir($this->baseDir);
	    
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
	    $folder->setBaseDir($this->baseDir);
	    
	    $data = $event->getData();
	    if ($folder->getFiles() !== null) {
	        $er = $this->em->getRepository(File::class);
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
	    $folder->setBaseDir($this->baseDir);
	    
	    if ($folder->getFiles() === null) {
	        $er = $this->em->getRepository(File::class);
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
