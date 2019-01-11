<?php 

namespace Puzzle\LearningBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Puzzle\LearningBundle\Event\PostEvent;
use Doctrine\ORM\EntityManager;
use Puzzle\MediaBundle\Service\FileManager;

class PostListener
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
	 * Post created on disk
	 * 
	 * @param PostEvent $event
	 */
	public function onCreate(PostEvent $event)
	{
	    $post = $event->getPost();
	    $data = $event->getData();
		
		if (file_exists($post->getAbsolutePath()) === false) {
		    return mkdir($post->getAbsolutePath(), 0777, true);
		}
		
		return true;
	}
	
	/**
	 * Rename post on disk
	 *
	 * @param PostEvent $event
	 */
	public function onUpdate(PostEvent $event) {
	    $post = $event->getPost();
	    $data = $event->getData();
	    if (file_exists($post->getAbsolutePath()) === false) {
	        return rename($data['oldAbsolutePath'], $post->getAbsolutePath());
	    }
	    
	    return true;
	}
	
	/**
	 * Remove post on disk
	 *
	 * @param PostEvent $event
	 */
	public function onRemove(PostEvent $event)
	{
	    $post = $event->getPost();
	    $postPath = $post->getAbsolutePath();
	    if (file_exists($postPath) === true) {
	        unlink($postPath);
	    }
	    
	    return true;
	}
	
	
	/**
	 * Add file to post
	 *
	 * @param PostEvent $event
	 */
	public function onAddFile(PostEvent $event) {
	    $post = $event->getPost();
	    $data = $event->getData();
	    $folder = null;
	    
	    if (isset($data['context']) && $data['context'] !== null) {
	        $folder = $this->fm->createFolder($data['context'], $post->getUser(), true);
	    }
	    
	    if ($folder !== null && isset($data['path']) && $data['path'] !== null) {
	        $file = $this->em->getRepository("MediaBundle:File")->findOneBy(['path' => $data['path']]);
	        if ($file !== null && file_exists($file->getAbsolutePath())) {
	            $file = $this->fm->copyFile($file, $folder);
	            $post->setPicture($folder->getPath().'/'.$file->getName());
	            $this->em->flush($post);
	        }
	    }
	    
	    return true;
	}
}

?>
