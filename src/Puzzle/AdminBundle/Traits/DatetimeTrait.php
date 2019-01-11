<?php
namespace Puzzle\AdminBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatetimeTrait
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 * 
 * @ORM\HasLifecycleCallbacks()
 */
trait DatetimeTrait
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(){
    	$this->createdAt = new \DateTime();
    }
    
    public function getCreatedAt(){
    	return $this->createdAt;
    }
    
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(){
    	$this->updatedAt = new \DateTime();
    }
    
    public function getUpdatedAt(){
        return $this->updatedAt;
    }
    
    public function convertInterval(){
        $interval = date_diff($this->updatedAt, new \DateTime("now"));
        $dateString = null;
        
        if (($interval->format('%a')) > 0) {
            $dateString = $interval->format('%a jrs');
        }elseif (($interval->format('%H')) > 0) {
            $dateString = $interval->format('%H h');
        }elseif ($interval->format("%i") > 0) {
            $dateString = $interval->format('%i mn');
        }else {
            $dateString = $interval->format('%is s');
        }
        
        return $dateString;
    }
}
