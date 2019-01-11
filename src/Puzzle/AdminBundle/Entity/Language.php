<?php

namespace Puzzle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\Nameable;
use Doctrine\Common\Collections\Collection;

/**
 * Language
 *
 * @ORM\Table(name="admin_language")
 * @ORM\Entity(repositoryClass="Puzzle\AdminBundle\Repository\LanguageRepository")
 */
class Language
{
    use PrimaryKeyTrait, Nameable;
}

