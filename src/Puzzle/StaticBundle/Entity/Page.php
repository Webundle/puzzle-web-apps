<?php
namespace Puzzle\StaticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Tree\Node;

/**
 * Page
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="static_page")
 * @ORM\Entity(repositoryClass="Puzzle\StaticBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Page
{
    use PrimaryKeyTrait,
        Blameable,
        Timestampable,
        Sluggable
    ;
    
    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;
    
	/**
	 * @var string
	 * @ORM\Column(name="content", type="text", nullable=true)
	 */
    private $content;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;
    
	/**
	 * @ORM\ManyToOne(targetEntity="Template")
	 * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $template;
	
	
	public function getSluggableFields() {
	    return ['name'];
	}

	public function setName($name) :self {
	    $this->name = $name;
	    return $this;
	}
	
	public function getName() :?string {
	    return $this->name;
	}
	
	public function setContent($content) :self {
		$this->content = $content;	
		return $this;
	}
	
	public function getContent() :?string {
		return $this->content;
	}
	
	public function setPicture($picture) :self {
	    $this->picture = $picture;
	    return $this;
	}
	
	public function getPicture() :?string {
	    return $this->picture;
	}
	
	public function setTemplate(Template $template) :self  {
	    $this->template = $template;
	    return $this;
	}
	
	public function getTemplate() :?Template {
	    return $this->template;
	}
}
