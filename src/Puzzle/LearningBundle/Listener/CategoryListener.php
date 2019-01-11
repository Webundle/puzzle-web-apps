<?php 

namespace Puzzle\LearningBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Puzzle\LearningBundle\Event\CategoryEvent;
use Doctrine\ORM\EntityManager;
use Puzzle\MediaBundle\Service\FileManager;

class CategoryListener
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
	
	/**
	 * Category created on disk
	 * 
	 * @param CategoryEvent $event
	 */
	public function onCreate(CategoryEvent $event)
	{
	    $category = $event->getCategory();
	    $data = $event->getData();
		
		if (file_exists($category->getAbsolutePath()) === false) {
		    return mkdir($category->getAbsolutePath(), 0777, true);
		}
		
		return true;
	}
	
	/**
	 * Rename category on disk
	 *
	 * @param CategoryEvent $event
	 */
	public function onUpdate(CategoryEvent $event) {
	    $category = $event->getCategory();
	    $data = $event->getData();
	    if (file_exists($category->getAbsolutePath()) === false) {
	        return rename($data['oldAbsolutePath'], $category->getAbsolutePath());
	    }
	    
	    return true;
	}
	
	/**
	 * Remove category on disk
	 *
	 * @param CategoryEvent $event
	 */
	public function onRemove(CategoryEvent $event)
	{
	    $category = $event->getCategory();
	    $categoryPath = $category->getAbsolutePath();
	    if (file_exists($categoryPath) === true) {
	        $this->fm->removeDirectory($categoryPath);
	    }
	    
	    return true;
	}
	
	
	/**
	 * Add picture to category
	 *
	 * @param CategoryEvent $event
	 */
	public function onAddPicture(CategoryEvent $event) {
	    $category = $event->getCategory();
	    $data = $event->getData();
	    $folder = null;
	    
	    if (isset($data['context']) && $data['context'] !== null) {
	        $folder = $this->fm->createFolder($data['context'], $category->getUser(), true);
	    }
	    
	    if ($folder !== null && isset($data['path']) && $data['path'] !== null) {
	        $file = $this->em->getRepository("MediaBundle:File")->findOneBy(['path' => $data['path']]);
	        if ($file !== null && file_exists($file->getAbsolutePath())) {
	            $file = $this->fm->copyFile($file, $folder);
	            $category->setPicture($folder->getPath().'/'.$file->getName());
	            $this->em->flush($category);
	        }
	    }
	    
	    return true;
	}
}

?>
