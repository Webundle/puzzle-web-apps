<?php

namespace Puzzle\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="mail_category")
 * @ORM\Entity()
 */
class Category
{
	
	const MAIL_SENT = 'sent';
	const MAIL_SAVE = 'draft';
	const MAIL_SCHEDULED = 'scheduled';
	
	const MAIL_TRASH = 'trash';
	const MAIL_RECEIVED = 'received';
	
	const MAIL_SENT_DESC = 'Folder of sent mails';
	const MAIL_SAVE_DESC = 'Folder of drafts';
	const MAIL_SCHEDULED_DESC = 'Folder of scheduled mails';
	
	const MAIL_TRASH_DESC = 'Folder of mails moving into trash';
	const MAIL_RECEIVED_DESC = 'Folder of received mails';
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_system", type="boolean")
     */
    private $isSystem;
    
    /**
     * @ORM\OneToMany(targetEntity="Mail", mappedBy="category")
     */
    private $mails;
    

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
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mails = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add mails
     *
     * @param \Puzzle\MailBundle\Entity\Mail $mails
     * @return Category
     */
    public function addMail(\Puzzle\MailBundle\Entity\Mail $mails)
    {
        $this->mails[] = $mails;

        return $this;
    }

    /**
     * Remove mails
     *
     * @param \Puzzle\MailBundle\Entity\Mail $mails
     */
    public function removeMail(\Puzzle\MailBundle\Entity\Mail $mails)
    {
        $this->mails->removeElement($mails);
    }

    /**
     * Get mails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMails()
    {
        return $this->mails;
    }

    /**
     * Set isSystem
     *
     * @param boolean $isSystem
     *
     * @return Category
     */
    public function setIsSystem($isSystem)
    {
        $this->isSystem = $isSystem;

        return $this;
    }

    /**
     * Get isSystem
     *
     * @return boolean
     */
    public function getIsSystem()
    {
        return $this->isSystem;
    }

    /**
     * Set tag
     *
     * @param string $tag
     *
     * @return Category
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
