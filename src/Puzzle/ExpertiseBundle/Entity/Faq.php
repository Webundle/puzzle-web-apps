<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Faq
 *
 * @ORM\Table(name="expertise_faq")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\FaqRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Faq
{
    use PrimaryKeyTrait, Timestampable;

    /**
     * @var string
     * @ORM\Column(name="question", type="string", length=255)
     */
    private $question;

    /**
     * @var string
     * @ORM\Column(name="answer", type="text")
     */
    private $answer;

    public function setQuestion(string $question) : self {
        $this->question = $question;
        return $this;
    }

    public function getQuestion() :? string {
        return $this->question;
    }

    public function setAnswer(string $answer) : self {
        $this->answer = $answer;
        return $this;
    }

    public function getAnswer() :? string {
        return $this->answer;
    }
}
