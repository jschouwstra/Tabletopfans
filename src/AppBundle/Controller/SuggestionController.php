<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Game;
use AppBundle\Entity\PlayLog;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\UserBundle\FOSUserBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use function var_dump;


/**
 * @Route("/suggestion")
 */
class SuggestionController extends Controller
{

    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/show", name="suggestion_test")
     *
     */
    public function suggestionShow()
    {
        $leastPlayedGames = $this->getLeastPlayedGames();
        $mostPlayedGames = $this->getMostPlayedGames();
        $getUnplayedGames = $this->getUnplayedGames();

        return $this->render('suggestion/show.html.twig', array(
            'leastPlayedGames' => $leastPlayedGames,
            'mostPlayedGames' => $mostPlayedGames,
            'unplayedGames' => $getUnplayedGames
        ));

    }

    /**
     * @Route("/show", name="suggestion_show")
     *
     */
    public function getMostPlayedGames()
    {

        $em = $this->getDoctrine()->getManager();
        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $query = $qb->c
            ->select('g.name, count(p.game) as plays')
            ->from(Game::class, 'g')
            ->where('p.user_id =' . $userID)
            ->andWhere('g.hidden = 0')
            ->leftJoin('g.playlogs', 'p')
            ->groupby('g.name', 'p.user_id')
            ->having('count(plays) > 0')
            ->orderBy('plays', 'desc')
            ->setMaxResults(5)
            ->getQuery();

        $results = $query->getResult();
        var_dump($results);
        die();
        return $results;
    }


    public function getLeastPlayedGames()
    {
        /** @var User $user */
        $user = $this->getUser();
        $userID = $user->getId();
        $manager = $this->getDoctrine()->getManager();
        /** @var QueryBuilder $qb */
        $qb = $manager->createQueryBuilder();

        $query = $qb
            ->select('g.name, count(p.game) as plays')
            ->from(PlayLog::class, 'p')
            ->where('p.user_id =' . $userID)
            ->andWhere('g.hidden = 0')
            ->leftJoin('p.game', 'g')
            ->groupby('g.name', 'p.user_id')
            ->having('count(plays) > 0')
            ->orderBy('plays', 'asc')
            ->setMaxResults(5)
            ->getQuery();

        $results = $query->getResult();

        return $results;
    }

    public function getUserGamesWithPlayCount()
    {
        /**
         * Get all User's games with playlog count
         * e.g User 1: Monopoly with (3) plays
         */
        $user = $this->getUser();
        $userGames = $user->getGames();
        $currentUserGamesWithPlayCount = array();
        foreach ($userGames as $game) {
            $currentUserGamesWithPlayCount[] = array(
                'name' => $game->getName(),
                'plays' => count($game->getPlaylogs())
            );
        }
        return $currentUserGamesWithPlayCount;

    }

    public function getUnplayedGames()
    {
        /**
         * Get all User's games with playlog count
         * e.g User 1: Monopoly with (3) plays
         */
        $user = $this->getUser();
        $userGames = $user->getGames();
        $currentUnplayedGames = array();
        foreach ($userGames as $game) {
            $numberOfPlays = count($game->getPlaylogs());

            //If game has zero plays
            if ($numberOfPlays == 0) {

                //If game is not hidden
                if ($game->getHidden() == 0) {

                    //Add to array
                    $currentUnplayedGames[] = array(
                        'name' => $game->getName(),
                        'plays' => count($game->getPlaylogs())
                    );
                }
            }
        }
        return $currentUnplayedGames;

    }


}
