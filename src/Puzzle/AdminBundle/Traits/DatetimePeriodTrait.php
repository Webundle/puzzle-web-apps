<?php
namespace Puzzle\AdminBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatetimePeriodTrait
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 * 
 * @ORM\HasLifecycleCallbacks()
 */
trait DatetimePeriodTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     */
    private $startedAt;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="ended_at", type="datetime", nullable=true)
     */
    private $endedAt;
    
    public function setStartedAt($startedAt = null) {
        if ($startedAt) {
            $this->startedAt = is_string($startedAt) === true ? new \DateTime($startedAt) : $startedAt;
        }
    	
    	return $this;
    }
    
    public function getStartedAt(){
    	return $this->startedAt;
    }
    
    public function setEndedAt($endedAt = null){
        if ($endedAt) {
            $this->endedAt = is_string($endedAt) === true ? new \DateTime($endedAt) : $endedAt;
        }
        
        return $this;
    }
    
    public function getEndedAt(){
    	return $this->endedAt;
    }
}
