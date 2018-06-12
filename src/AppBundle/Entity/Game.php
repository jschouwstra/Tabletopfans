<?php

namespace AppBundle\Entity;

use AppBundle\Controller\GameController;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Nataniel\BoardGameGeek\Thing;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="game" , uniqueConstraints={@UniqueConstraint(name="unique", columns={"bgg_id", "name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
{
    /****************************************
     *
     *          Relations
     *
     ***************************************/

    /**
     * @ORM\OneToMany(targetEntity="PlayLog", mappedBy="game")
     */
    private $playlogs;

    /**
     * @ORM\OneToMany(targetEntity="Expansion", mappedBy="game")
     */
    private $expansions;

    /**
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $users;

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
     * @ORM\Column(name="bgg_id", type="integer", length=255)
     */
    private $bgg_id;


    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="no_of_players", type="string", length=255, nullable=true)
     */
    private $no_of_players;


    /**
     * @var string
     * @ORM\Column(name="playtime", type="string", length=255, nullable=true)
     */
    private $playtime;


    /**
     * @var string
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;



    /**
     * @ORM\Column(name="is_expansion",nullable=false, type="integer", options={"unsigned": true, "default" :0})
     */
    private $isExpansion;


    /****************************************
     *
     *          Methods
     *
     ***************************************/


    /**
     * @return mixed
     */
    public function getisExpansion()
    {
        return $this->isExpansion;
    }

    /**
     * @param mixed $isExpansion
     */
    public function setIsExpansion($isExpansion)
    {
        $this->isExpansion = $isExpansion;
    }

  
    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }


    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->playlogs = new ArrayCollection();
        $this->expansions = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * Get bgg_id
     *
     * @return int
     */
    public function getBggId()
    {
        return $this->bgg_id;
    }

    /**
     * Set bgg_id
     *
     */
    public function setBggId($bgg_id)
    {
        $this->bgg_id = $bgg_id;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Game
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

//array collection functions

    /**
     * @return mixed
     */
    public function getPlaylogs()
    {
        return $this->playlogs;
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
     * @return mixed
     */
    public function getNoOfPlayers()
    {
        return $this->no_of_players;
    }

    /**
     * @param mixed $no_of_players
     */
    public function setNoOfPlayers($no_of_players)
    {
        $this->no_of_players = $no_of_players;
    }

    /**
     * @return string
     */
    public function getPlaytime()
    {
        return $this->playtime;
    }

    /**
     * @param string $playtime
     */
    public function setPlaytime($playtime)
    {
        $this->playtime = $playtime;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getPlays()
    {
        /**
         * @var GameController $gamecontroller
         */
        $plays = $gamecontroller->getPlayCountByGameId($this->getId());
        return $plays;
    }

}
