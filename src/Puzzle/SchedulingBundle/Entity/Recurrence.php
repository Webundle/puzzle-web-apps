<?php
namespace Puzzle\SchedulingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recurrence
 *
 * @ORM\Table(name="scheduling_recurrence")
 * @ORM\Entity
 *
 */
class Recurrence
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\UserBundle\Service\KeygenManager")
     * 
     */
    private $id;

	/**
	 * @ORM\Column(name="intervale", type="string", length=255)
	 */
	private $intervale;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="unity", type="string", length=255)
	 */
	private $unity;
	
	/**
	 * @ORM\Column(name="excluded_days", type="simple_array", nullable=true)
	 */
	private $excludedDays;
	
	/**
	 * @ORM\Column(name="target_app_name", type="string", nullable=true)
	 */
	private $targetAppName;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="target_entity_name", type="string", length=255)
	 */
	private $targetEntityName;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="target_entity_id", type="string", length=255)
	 */
	private $targetEntityId;
    
    /**
     * @ORM\Column(name="due_at", type="datetime", nullable=true)
     */
    private $dueAt;
    
    /**
     * @ORM\Column(name="last_run_at", type="datetime", nullable=true)
     */
    private $lastRunAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_run_at", type="datetime", nullable=true)
     */
    private $nextRunAt;
    
    
    /**
     * @var array
     *
     * @ORM\Column(name="timezone", type="simple_array", nullable=true)
     */
    private $timezone;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="job", type="string", length=255, nullable=true)
     *
     */
    private $job;
    
    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
    	return $this->id;
    }
	
	/**
	 * Set excludedDays
	 *
	 * @param array $excludedDays
	 * @return Recurrence
	 */
	public function setExcludedDays($excludedDays){
		$this->excludedDays = $excludedDays;
	
		return $this;
	}
	
	/**
	 * Add excludedDay
	 *
	 * @param string $excludedDay
	 */
	public function addExcludedDay($excludedDay){
		$this->excludedDays[] = $excludedDay;
		$this->excludedDays = array_unique($this->excludedDays);
	
		return $this;
	}
	
	
	/**
	 * Remove excludedDay
	 *
	 * @param string $excludedDay
	 */
	public function removeExcludedDay(string $excludedDay){
		$this->excludedDays = array_diff($this->excludedDays, [$excludedDay]);
		 
		return $this;
	}
	
	/**
	 * Get excludedDays
	 *
	 * @return array
	 */
	public function getExcludedDays(){
		return $this->excludedDays;
	}
	

    /**
     * Set intervale
     *
     * @param string $intervale
     * @return Recurrence
     */
    public function setIntervale($intervale)
    {
        $this->intervale = $intervale;

        return $this;
    }

    /**
     * Get intervale
     *
     * @return string 
     */
    public function getIntervale(){
        return $this->intervale;
    }

    
    /**
     * Set unity
     *
     * @param string $unity
     * @return Recurrence
     */
    public function setUnity($unity)
    {
        $this->unity = $unity;

        return $this;
    }

    /**
     * Get unity
     *
     * @return string 
     */
    public function getUnity(){
        return $this->unity;
    }


    /**
     * Set targetAppName
     *
     * @param string $targetAppName
     * @return Recurrence
     */
    public function setTargetAppName($targetAppName)
    {
        $this->targetAppName = $targetAppName;

        return $this;
    }

    /**
     * Get targetAppName
     *
     * @return string 
     */
    public function getTargetAppName(){
        return $this->targetAppName;
    }

    /**
     * Set targetEntityName
     *
     * @param string $targetEntityName
     * @return Recurrence
     */
    public function setTargetEntityName($targetEntityName)
    {
        $this->targetEntityName = $targetEntityName;

        return $this;
    }

    /**
     * Get targetEntityName
     *
     * @return string 
     */
    public function getTargetEntityName(){
        return $this->targetEntityName;
    }

    /**
     * Set targetEntityId
     *
     * @param string $targetEntityId
     * @return Recurrence
     */
    public function setTargetEntityId($targetEntityId)
    {
        $this->targetEntityId = $targetEntityId;

        return $this;
    }

    /**
     * Get targetEntityId
     *
     * @return string 
     */
    public function getTargetEntityId(){
        return $this->targetEntityId;
    }

    /**
     * Set lastRunAt
     *
     * @param \DateTime $lastRunAt
     * @return Recurrence
     */
    public function setLastRunAt($lastRunAt)
    {
        $this->lastRunAt = $lastRunAt;

        return $this;
    }

    /**
     * Get lastRunAt
     *
     * @return \DateTime 
     */
    public function getLastRunAt(){
        return $this->lastRunAt;
    }

    /**
     * Set nextRunAt
     *
     * @param \DateTime $nextRunAt
     * @return Recurrence
     */
    public function setNextRunAt($nextRunAt)
    {
        $this->nextRunAt = $nextRunAt;

        return $this;
    }

    /**
     * Get nextRunAt
     *
     * @return \DateTime 
     */
    public function getNextRunAt(){
        return $this->nextRunAt;
    }

    /**
     * Set timezone
     *
     * @param array $timezone
     * @return Recurrence
     */
    public function setTimezone($timezone){
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return array 
     */
    public function getTimezone(){
        return $this->timezone;
    }

    /**
     * Set dueAt
     *
     * @param \DateTime $dueAt
     *
     * @return Recurrence
     */
    public function setDueAt($dueAt){
        $this->dueAt = $dueAt;

        return $this;
    }

    /**
     * Get dueAt
     *
     * @return \DateTime
     */
    public function getDueAt(){
        return $this->dueAt;
    }

    /**
     * Set job
     *
     * @param string $job
     *
     * @return Recurrence
     */
    public function setJob($job){
        $this->job = $job;
        
        return $this;
    }

    /**
     * Get job
     *
     * @return string
     */
    public function getJob(){
        return $this->job;
    }
}
