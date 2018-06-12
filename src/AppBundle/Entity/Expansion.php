<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\UniqueConstraint;


/**
 * Expansion
 *
 * @ORM\Table(name="expansion",uniqueConstraints={@UniqueConstraint(name="unique", columns={"bgg_id", "name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ExpansionRepository")
 */
class Expansion
{
    /**************************
     *  Relations
     *************************/

    /**
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="expansions")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\PlayLog", mappedBy="expansions")
     */
    private $playlog;



    /**************************
     *  Properties
     *************************/




    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = "3",
     *  max = "100"
     * )
     * @ORM\Column(name="name", type="string", length=255, unique=false)
     */
    private $name;


    /**
     * @var int
     * @ORM\Column(name="bgg_id", type="integer", length=255)
     */
    private $bgg_id;

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

    /**************************
     *  Getters and setters
     *************************/
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
    public function getBggId()
    {
        return $this->bgg_id;
    }

    /**
     * @param int $bgg_id
     */
    public function setBggId($bgg_id)
    {
        $this->bgg_id = $bgg_id;
    }

    /**
     * @return string
     */
    public function getNoOfPlayers()
    {
        return $this->no_of_players;
    }

    /**
     * @param string $no_of_players
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
     * @return mixed
     */
    public function getPlaylog()
    {
        return $this->playlog;
    }

    /**
     * @param mixed $playlog
     */
    public function setPlaylog($playlog)
    {
        $this->playlog = $playlog;
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
     * Set name
     *
     * @param string $name
     *
     * @return Expansion
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

    //Array collection functions
    //  Every function about relations

    public function getExpansions()
    {
        return $this->expansions;
    }

    /**
     * @param mixed $games
     */
    public function setGames($games)
    {
        $this->games = $games;
    }

    public function addGame(Game $game)
    {
        $this->games->add($game);
        //     $this->games[] = $game;
        return $this;
    }



}

