<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Game;
use AppBundle\Entity\User;

/**
 * Created by PhpStorm.
 * User: Jelle
 */
class SuggestionRepository extends \Doctrine\ORM\EntityRepository
{
    public function findLeastPlayedGames($user_id)
    {

        /** var User $user */
        $userGames = $user->getGames();
        foreach ($userGames as $game) {
            /** @var Game $game * */
            return $game->getPlaylogs();

        }


    }
}