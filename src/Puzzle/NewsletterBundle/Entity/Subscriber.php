<?php

namespace Puzzle\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Doctrine\Common\Collections\Collection;

/**
 * Subscriber
 *
 * @ORM\Table(name="newsletter_subscriber")
 * @ORM\Entity(repositoryClass="Puzzle\NewsletterBundle\Repository\SubscriberRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Subscriber
{
    use PrimaryKeyTrait, Timestampable, Blameable;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="subscribers")
     * @ORM\JoinTable(name="susbcriber_group",
     *      joinColumns={@ORM\JoinColumn(name="subscriber_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private $groups;
    
    public function setName($name) :self {
        $this->name = $name;
        return $this;
    }

    public function getName() :?string {
        return $this->name;
    }

    public function setEmail($email) :self {
        $this->email = $email;
        return $this;
    }

    public function getEmail() :?string {
        return $this->email;
    }
    
    public function setGroups (Collection $groups) :self {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }
        
        return $this;
    }
    
    public function addGroup(Group $group) :self {
        if ($this->groups->count() === 0 || $this->groups->contains($group) === false) {
            $this->groups->add($group);
        }
        
        return $this;
    }
    
    public function removeGroup(Group $group) :self {
        if ($this->groups->contains($group) === true) {
            $this->groups->removeElement($group);
        }
        
        return $this;
    }
    
    public function getGroups() :?Collection {
        return $this->groups;
    }
}
