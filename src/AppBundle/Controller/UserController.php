<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Game;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function var_export;


class UserController extends Controller
{

    /**
     * Adds game(s) to current user.
     *
     * @Route("/user/game/add", name="game_add")
     * @Method({"GET", "POST"})
     */
    public function addGameAction(Request $request)
    {
        /**
         * @var Game $game
         */

        /** @var  $form */
        $form = $this->createForm('AppBundle\Form\addGameToUserType');
        $addGameForm = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('add', SubmitType::class, array('label' => 'Add Game'))
            ->getForm();

        $form->handleRequest($request);

        /** Get current User
         * @var User $userObject
         */
        $userObject = $this->getUser();

        //Add game
        //If form is not submitted get all user's games and set the selectbox accordingly
        if (!$form->isSubmitted()) {
            // games selecteren
            $form["game"]->setData($userObject->getGames());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //get form data as array
            /** @var  $game */
            $gameArray = $form["game"]->getData();

            /**
             * Unset each game from User Collection and set each game with ArrayCollection "->remove" and "->add"
             * Remove every unselected and add everything selected to the User object
             */
            $userObject->removeAllGames();
            foreach ($gameArray as $game) {
                $userObject->addGame($game);
            }

            $em->persist($userObject);
            $em->flush();
            return $this->redirectToRoute('game_index');
        }

        return $this->render('user/addGame.html.twig', array(
            'form' => $form->createView(),
            'addGameForm' => $addGameForm->createView()

        ));
    }

    public function getUserGamesForGraph()
    {
        $games = array();
        $gameplayArray = array();

        $totalGames = array();
        $totalGameplays = array();

        $user = $this->getUser();
        $userGames = $user->getGames();

        foreach ($userGames as $game) {
            //games arrays
            array_push($games, $game->getName());
            $gameplays = array();

            /*
             * playlogs array
             * all playlogs of current user AND current game
            */

            $playlogs = $game->getPlaylogs();
            foreach ($playlogs as $playlog) {
                if ($user->getId() == $playlog->getUserId()) {
                    array_push($gameplays, 1);
                }
            }

            array_push($gameplayArray, count($gameplays));
        }

        for ($x = 0; $x < count($gameplayArray); $x++) {

            if ($gameplayArray[$x] != null) {
                array_push($totalGameplays, $gameplayArray[$x]);
                array_push($totalGames, $games[$x]);

            }
        }
        return array($totalGames, $totalGameplays);
    }

    /**
     * Show current user's dashboard.
     *
     * @Route("user/dashboard", name="user_dashboard")
     * @Method({"GET", "POST"})
     */
    public function showDashboard()
    {
        $userGames = $this->getUserGamesForGraph();
        $totalGames = $userGames[0];
        $totalGameplays = $userGames[1];
        return $this->render('user/dashboard.html.twig', array(
            'games' => $totalGames,
            'gameplays' => $totalGameplays,
        ));
    }


}