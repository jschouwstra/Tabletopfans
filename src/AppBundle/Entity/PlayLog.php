<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * PlayLog
 *
 * @ORM\Table(name="play_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayLogRepository")
 */
class PlayLog
{
    /****************************************
     *
     *          Relations
     *
     ***************************************/
    /**
     * @ORM\ManyToMany(targetEntity="Expansion", inversedBy="playlog")
     *
     * @ORM\JoinTable(name="playlog_expansion",
     *      joinColumns={@ORM\JoinColumn(name="playlog_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="expansion_id", referencedColumnName="id")}
     *      )
     */
    private $expansions;

    /**
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="playlogs")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="playlogs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct()
    {
        $this->expansions = new ArrayCollection();
    }
    /****************************************
     *
     *          Properties
     *
     ***************************************/

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var int
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $user_id;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255, unique=false, nullable=true)
     */
    private $description;

    /****************************************
     *
     *          Methods
     *
     ***************************************/

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }


    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return PlayLog
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }


    /**
     * @return mixed
     */
    public function getExpansions()
    {
        return $this->expansions;
    }

    /**
     * @param mixed $expansions
     */
    public function setExpansions($expansions)
    {
        $this->expansions = $expansions;
    }

    public function addExpansion(Expansion $expansion)
    {
        $this->expansions->add($expansion);
        return $this;
    }

    public function removeExpansion(Expansion $expansion)
    {
        $this->expansions->removeElement($expansion);
    }

    public function removeAllExpansions()
    {
        foreach ($this->expansions as $expansion) {
            $this->removeExpansion($expansion);
        }
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


}

