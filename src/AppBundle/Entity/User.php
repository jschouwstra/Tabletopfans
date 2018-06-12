<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\ManyToMany(targetEntity="Game")
     *
     * @ORM\JoinTable(name="user_game",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")}
     * )
     *
     * @ORM\OrderBy({"name" = "ASC"})
     *
     */
    private $games;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlayLog", mappedBy="user")
     *
     */
    private $playlogs;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @return mixed
     */
    public function getGames()
    {
        return $this->games;
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

    public function removeGame(Game $game)
    {
        $this->games->removeElement($game);
    }

    public function removeAllGames()
    {
        foreach ($this->games as $game) {
            $this->removeGame($game);
        }
    }

    /**
     * @return mixed
     */
    public function getPlaylogs()
    {
        return $this->playlogs;
    }

    /**
     * @param mixed $playlogs
     */
    public function setPlaylogs($playlogs)
    {
        $this->playlogs = $playlogs;
    }

    public function addPlayLog(PlayLog $playlog)
    {
        $this->playlog->add($playlog);
        $playlog->setPlayLogs($this);
    }


}