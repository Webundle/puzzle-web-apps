<?php
namespace Puzzle\AdminBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Taggable
 * 
 * @author AGNES Gnagne Cedric <cecnho55@gmail.com>
 * 
 */
trait Taggable
{
    /**
     * @ORM\Column(name="tag", type="simple_array", nullable=true)
	 * @var array
	 */
	private $tags;
    
	public function setTags($tags) : self {
	    $this->tags = $tags;
	    return $this;
	}
	
	public function addTag($tag) : self {
	    $this->tags[] = $tag;
	    $this->tags = array_unique($this->tags);
	    
	    return $this;
	}
	
	public function removeTag($tag) : self {
	    $this->tags = array_diff($this->tags, [$tag]);
	    return $this;
	}

	public function getTags() {
	    return $this->tags;
	}
}
