<?php
namespace Puzzle\LearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * Comment vote
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 * 
 * @ORM\Table(name="learning_comment_vote")
 * @ORM\Entity(repositoryClass="Puzzle\LearningBundle\Repository\CommentVoteRepository")
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
