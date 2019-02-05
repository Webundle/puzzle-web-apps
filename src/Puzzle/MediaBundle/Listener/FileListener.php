<?php 

namespace Puzzle\MediaBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Service\FileManager;
use Puzzle\MediaBundle\Entity\File;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class FileListener
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
	
	/**
	 * Post created on disk
	 * 
	 * @param FileEvent $event
	 */
	public function onCreate(FileEvent $event) {
	    $data = $event->getData();
		
		if (file_exists($data['absolutePath']) === false) {
		    return mkdir($data['absolutePath'], 0777, true);
		}
		
		return true;
	}
	
	/**
	 * Folder class load
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function postLoad(LifecycleEventArgs $args) {
	    $file = $args->getEntity();
	    
	    if (!$file instanceof File) {
	        return;
	    }
	    
	    $file->setBaseDir($this->baseDir);
	}
	
	/**
	 * Rename post on disk
	 *
	 * @param FileEvent $event
	 */
	public function onRename(FileEvent $event) {
	    $data = $event->getData();
	    if (file_exists($data['absolutePath']) === false) {
	        return rename($data['oldAbsolutePath'], $data['absolutePath']);
	    }
	    
	    return true;
	}
	
	/**
	 * Remove file on disk
	 *
	 * @param FileEvent $event
	 */
	public function onRemove(FileEvent $event) {
	    $data = $event->getData();
	    if (file_exists($data['absolutePath']) === true) {
	        unlink($data['absolutePath']);
	    }
	    
	    return true;
	}
	
	
	/**
	 * Copy
	 *
	 * @param FileEvent $event
	 */
	public function onCopy(FileEvent $event) {
	    $data = $event->getData();
	    $closure = $data['closure'];
	    $folder = null;
	    
	    if (isset($data['context']) && $data['context'] !== null) {
	        $folder = $this->fm->createFolder($data['context'], $data['user'], true);
	    }
	    
	    if ($folder !== null && isset($data['path']) && $data['path'] !== null) {
	        $file = $this->em->getRepository(File::class)->findOneBy(['path' => $data['path']]);
	        // Copy existing file
	        $data['preserve_files'] = true;
	        if ($file === null) {
	            $file = $this->em->getRepository(File::class)->find($data['path']);
	            // Move file after upload
	            $data['preserve_files'] = false;
	        }
	        
	        if ($file !== null && file_exists($file->getAbsolutePath())) {
	            if (isset($data['preserve_files']) === true && $data['preserve_files'] === false) {
	                $this->fm->moveFile($file, $folder);
	            }else {
	                $isOverwritable = isset($data['is_overwritable']) ? $data['is_overwritable'] : false;
	                $this->fm->copyFile($file, $folder, $isOverwritable);
	            }
	            
	            $closure($folder->getPath().'/'.$file->getName());
	        }
	    }
	    
	    return true;
	}
}

?>
