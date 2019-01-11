<?php

namespace Puzzle\SchedulingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="scheduling_notification")
 * @ORM\Entity
 * 
 */
class Notification
{
	
	const CHANNEL_SMS = "sms";
	const CHANNEL_EMAIL = "email";
	const CHANNEL_IN_APP = "in_app";
	const CHANNEL_FACEBOOK = "facebook";
	
	const UNITY_MINUTE = 'PT1M';
	const UNITY_HOUR = "PT1H";
	const UNITY_DAY = "P1D";
	const UNITY_WEEK = "P1W";
	const UNITY_MONTH = "P1M";
	const UNITY_YEAR = "P1Y";
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
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=255)
     * 
     */
    private $channel;
    
    /**
     * @var string
     *
     * @ORM\Column(name="intervale", type="string", length=255)
     * 
     */
    private $intervale;
    
    /**
     * @var string
     *
     * @ORM\Column(name="unity", type="string", length=255)
     * 
     */
    private $unity;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=255, nullable=true)
     * 
     */
    private $template;
    
    /**
     * @ORM\Column(name="target_app_name", type="string", nullable=true)
     *
     */
    private $targetAppName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="target_entity_name", type="string", length=255)
     * 
     */
    private $targetEntityName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="target_entity_id", type="string", length=255)
     * 
     */
    private $targetEntityId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="job", type="string", length=255, nullable=true)
     * 
     */
    private $job;
    
    /**
     * @ORM\Column(name="command", type="string", nullable=true)
     */
    private $command;
    
    /**
     * @ORM\Column(name="command_args", type="simple_array", nullable=true)
     */
    private $commandArgs;
    
    /**
     * @var \DateTime
     *
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
     * Get id
     *
     * @return integer 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Set channel
     *
     * @param string $channel
     * @return Notification
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string 
     */
    public function getChannel()
    {
        return $this->channel;
    }


    /**
     * Set intervale
     *
     * @param string $intervale
     * @return Notification
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
    public function getIntervale()
    {
        return $this->intervale;
    }

    /**
     * Set template
     *
     * @param string $template
     * @return Notification
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }


    /**
     * Set unity
     *
     * @param string $unity
     * @return Notification
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
    public function getUnity()
    {
        return $this->unity;
    }


    /**
     * Set targetAppName
     *
     * @param string $targetAppName
     * @return Notification
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
    public function getTargetAppName()
    {
        return $this->targetAppName;
    }

    /**
     * Set targetEntityName
     *
     * @param string $targetEntityName
     * @return Notification
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
    public function getTargetEntityName()
    {
        return $this->targetEntityName;
    }

    /**
     * Set targetEntityId
     *
     * @param string $targetEntityId
     * @return Notification
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
    public function getTargetEntityId()
    {
        return $this->targetEntityId;
    }

    /**
     * Set lastRunAt
     *
     * @param \DateTime $lastRunAt
     * @return Notification
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
    public function getLastRunAt()
    {
        return $this->lastRunAt;
    }

    /**
     * Set nextRunAt
     *
     * @param \DateTime $nextRunAt
     * @return Notification
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
    public function getNextRunAt()
    {
        return $this->nextRunAt;
    }

    /**
     * Set timezone
     *
     * @param array $timezone
     * @return Notification
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return array 
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set job
     *
     * @param string $job
     * @return Notification
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return string 
     */
    public function getJob()
    {
        return $this->job;
    }
    
    /**
     * Set command
     *
     * @param string $command
     * @return Notification
     */
    public function setCommand($command)
    {
    	$this->command = $command;
    	
    	return $this;
    }
    
    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
    	return $this->command;
    }
    
    /**
     * Set commandArgs
     *
     * @param array $commandArgs
     * @return Notification
     */
    public function setCommandArgs(array $commandArgs)
    {
    	$this->commandArgs = $commandArgs;
    	
    	return $this;
    }
    
    /**
     * Get commandArgs
     *
     * @return array
     */
    public function getCommandArgs(){
    	return $this->commandArgs;
    }
    
    /**
     * Add commandArg
     *
     * @param string $commandArg
     */
    public function addCommandArg($commandArg){
    	$this->commandArgs[] = strtolower($commandArg);
    	$this->commandArgs = array_unique($this->commandArgs);
    	
    	return $this;
    }
    
    /**
     * Remove commandArg
     *
     * @param string $commandArg
     */
    public function removeCommandArg($commandArg){
    	
    	$this->commandArgs = array_diff($this->commandArgs, [$commandArg]);
    	
    	return $this;
    }
}
