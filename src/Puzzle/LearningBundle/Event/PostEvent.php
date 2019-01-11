<?php 

namespace Puzzle\LearningBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Puzzle\LearningBundle\Entity\Post;

class PostEvent extends Event
{
	/**
	 * @var Post
	 */
	private $post;
	
	/**
	 * @var array
	 */
	private $data;
	
	public function __construct(Post $post, array $data = null){
		$this->post= $post;
		$this->data = $data;
	}
	
	public function getPost(){
		return $this->post;
	}
	
	public function getData(){
	    return $this->data;
	}
	
}

?>