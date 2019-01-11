<?php
namespace Puzzle\CharityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Tree\Node;
use Knp\DoctrineBehaviors\Model\Tree\NodeInterface;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * Charity Category
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 * @ORM\Table(name="charity_category")
 * @ORM\Entity(repositoryClass="Puzzle\CharityBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category implements NodeInterface
{
    use PrimaryKeyTrait,
        Nameable,
        Describable,
        Pictureable,
        Timestampable,
        Blameable,
        Sluggable,
        Node
    ;

    /**
     * @var string
     * @ORM\Column(name="materializedPath", type="string", nullable=true)
     */
    protected $materializedPath = '';
    
    /**
     * @ORM\OneToMany(targetEntity="Cause", mappedBy="category")
     */
    private $causes;
    
    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parentNode")
     */
    private $childNodes;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="childNodes")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parentNode;
    
    /**
     * Constructor
     */
    public function __construct() {
    	$this->causes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function setCauses(Collection $causes) : self {
        $this->causes = $causes;
        return $this;
    }
    
    public function addCause(Cause $cause) : self {
        if ($this->causes === null || $this->causes->contains($cause) === false){
            $this->causes->add($cause);
        }
        
        return $this;
    }

    public function removeCause(Cause $cause) : self {
        $this->causes->removeElement($cause);
        return $this;
    }

    public function getCauses() :? Collection {
        return $this->causes;
    }
}
