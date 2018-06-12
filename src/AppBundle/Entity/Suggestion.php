<?php
/**
 * Created by PhpStorm.
 * User: Jelle
 * Date: 14-9-2017
 * Time: 20:49
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="suggestion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SuggestionRepository")
 */

class Suggestion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

}