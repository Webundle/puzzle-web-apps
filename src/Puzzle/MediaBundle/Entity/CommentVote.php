<?php
namespace Puzzle\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * Comment vote
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 * 
 * @ORM\Table(name="media_comment_vote")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class CommentVote
{
    use PrimaryKeyTrait, Timestampable, Blameable;
    
    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="votes")
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     */
    private $comment;

    public function setComment(Comment $comment = null) : self {
        $this->comment = $comment;
        return $this;
    }

    public function getComment() :? Comment {
        return $this->comment;
    }
}
