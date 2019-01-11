<?php

namespace Puzzle\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mail
 *
 * @ORM\Table(name="mail_mail")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Mail
{
	const MAIL_SENT = 'sent';
	const MAIL_SAVE = 'draft';
	const MAIL_SCHEDULED = 'scheduled';
	
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\UserBundle\Service\KeygenManager")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;
  
    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;
   
    /**
     * @var array
     *
     * @ORM\Column(name="receivers", type="simple_array", nullable=true)
     */
    private $receivers;
    
    /**
     * @var array
     *
     * @ORM\Column(name="attachments", type="simple_array", nullable=true)
     */
    private $attachments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sentAt", type="datetime", nullable=true)
     */
    private $sentAt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=50)
     */
    private $tag;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Mail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Mail
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
	* @ORM\PrePersist
	*/
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
    	$this->updatedAt = new \DateTime();
    	
    	return $this;
    }
    
    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
    	return $this->updatedAt;
    }

    /**
     * Set sentAt
     *
     * @param \DateTime $sentAt
     * @return Mail
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt
     *
     * @return \DateTime 
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
       
    }
    
    /**
     * Add attachment
     *
     * @param string $attachment
     */
    public function addAttachment($attachment){
    	
    	$this->attachments[] = $attachment;
    	$this->attachments = array_unique($this->attachments);
    	
    	return $this;
    }
    
    /**
     * Remove attachment
     *
     * @param unknown $attachment
     */
    public function removeAttachment($attachment){
    	
    	$pos = array_keys($this->attachments, $attachment);
    	array_splice($this->attachments, $pos[0], strlen($attachment));
    	
    	return $this;
    }
    
    /**
     * Get attachments
     *
     * @return array
     */
    public function getAttachments()
    {
    	return $this->attachments;
    }
    
    /**
     * Set attachments
     *
     * @param array $attachments
     *
     * @return Mail
     */
    public function setAttachments($attachments)
    {
    	$this->attachments = $attachments;
    	
    	return $this;
    }
    
    /**
     * Add receiver
     *
     * @param string $receiver
     */
    public function addReceiver($receiver){
    	
    	$this->receivers[] = $receiver;
    	$this->receivers = array_unique($this->receivers);
    	
    	return $this;
    }
    
    /**
     * Remove receiver
     *
     * @param unknown $receiver
     */
    public function removeReceiver($receiver){
    	
    	$pos = array_keys($this->receivers, $receiver);
    	array_splice($this->receivers, $pos[0], strlen($receiver));
    	
    	return $this;
    }
    
    /**
     * Get receivers
     *
     * @return array
     */
    public function getReceivers()
    {
    	return $this->receivers;
    }
    
    /**
     * Set receivers
     *
     * @param array $receivers
     *
     * @return Mail
     */
    public function setReceivers($receivers)
    {
    	$this->receivers = $receivers;
    	
    	return $this;
    }

    /**
     * Set tag
     *
     * @param string $tag
     *
     * @return Mail
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }
}
