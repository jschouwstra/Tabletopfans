<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\PlayLog;
use AppBundle\Entity\User;

use function array_push;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use function file_get_contents;
use function is_numeric;
use JMS\Serializer\Serializer;
use function json_decode;
use function json_encode;
use Nataniel\BoardGameGeek\Client;
use function simplexml_load_file;
use function simplexml_load_string;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\ResultSetMapping;
use function var_dump;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


/**
 * Game controller.
 *
 * @Route("game")
 */
class GameController extends Controller
{
    /**
     * Lists all user's game entities.
     *
     * @Route("/", name="game_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        /*
         * The FOSUser object (current user) is injected in the container so we can access it globally
         *
        */
        /** @var User $usr */
        $usr = $this->getUser();
        $userGames = $usr->getGames();
        $games = array();


        return $this->render('game/index.html.twig', array(
            'games' => $userGames,
            'max_limit_error' => 25,
        ));
    }

    /**
     * Lists all user's game entities as JSON.
     *
     * @Route("/user/json", name="user_games_json")
     * @Method("GET")
     */
    public function returnUserGamesAsJson(Request $request)
    {
        /*
         * The FOSUser object (current user) is injected in the container so we can access it globally
         *
        */

        /** @var User $usr */
        $usr = $this->getUser();
        $userGames = $usr->getGames();

        $games = array();
        foreach ($userGames as $game) {
            array_push($games, $game->getName());
        }
        $serializer = $this->get('jms_serializer');

        $jsonContent = $serializer->serialize($games, 'json');
        return new Response(
            $jsonContent
        );
    }

    /**
     * Lists all game entities as JSON.
     *
     * @Route("/all/json", name="find_games_json")
     * @Method("GET")
     */
    public function returnAllGamesAsJson(Request $request)
    {
        //Use existing GameRepository, Appbundle:Game
        $gameRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Game');
        $name = $request->get('name');
        $games = $gameRepository->findByName($name);

        $serializer = $this->get('jms_serializer');

        $jsonContent = $serializer->serialize($games, 'json');
        return new Response(
            $jsonContent
        );
    }


    /**
     *
     * Finds and displays a game entity.
     *
     * @Route("/{id}", name="game_show")
     * @Method("GET")
     */
    public function showAction(Game $game, $id)
    {
        $gameId = $id;
        /**
         * User $user
         */
        $userId = $this->getUser()->getId();

        /**
         * Game $game
         */
        $game = $this->getDoctrine()
            ->getRepository(Game::class)
            ->find($gameId);

        return $this->render('game/show.html.twig', array(
            'game' => $game,
            'plays' => $this->getPlaysByGameId($gameId)
        ));
    }

    public function getPlaysByGameId($gameId)
    {
        $userId = $this->getUser()->getId();
        $manager = $this->getDoctrine()->getManager();

        $qb = $manager->createQueryBuilder();
        $query = $qb->select('p')
            ->from(PlayLog::class, 'p')
            ->where('p.user_id = ' . $userId)
            ->andWhere('p.game = ' . $gameId)
            ->getQuery();

        $plays = count($query->getResult());
        return $plays;
    }

    /**
     * Displays a form to edit an existing game entity.
     *
     * @Route("/{id}/edit", name="game_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Game $game)
    {
        $deleteForm = $this->createDeleteForm($game);
        $editForm = $this->createForm('AppBundle\Form\GameType', $game);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_show', array('id' => $game->getId()));
        }

        return $this->render('game/edit.html.twig', array(
            'game' => $game,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Removes a game entity from User collection.
     *
     * @Route("/remove/game/user", name="remove_user_game")
     */
    public function removeGameFromUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $gameId = json_decode($request->getContent());
        $game = $em->getRepository(Game::class)->find($gameId);
        $user = $this->getUser();

        /** @var User $user */
        $user->removeGame($game);
        $em->persist($user);
        $em->flush();
//        return $this->redirectToRoute('game_index');

        $this->addFlash('warning', 'Game ' . $game->getName() . ' removed');

        return new Response("Game remove from User collection: success");

    }

    /**
     * Deletes a game entity.
     *
     * @Route("/{id}", name="game_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Game $game)
    {
        $form = $this->createDeleteForm($game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($game);
            $em->flush($game);
        }

        return $this->redirectToRoute('game_index');
    }

    /**
     * Creates a form to delete a game entity.
     *
     * @param Game $game The game entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Game $game)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('game_delete', array('id' => $game->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/add/expansion/to/{gameId}", name="add_expansion_to_game")
     * @Method({"GET", "POST"})
     */
    public function addExpansionAction(Game $gameId, Request $request)
    {
        $form = $this->createForm('AppBundle\Form\addExpansionToGameType');
        $form->handleRequest($request);
        $game = $gameId;

        if (!$form->isSubmitted()) {
            //if form not submitted set current known data
            $form["expansion"]->setData($game->getExpansions());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $expansionArray = $form["expansion"]->getData();
            $game->removeAllExpansions();
            foreach ($expansionArray as $expansion) {
                $game->addExpansion($expansion);
            }

            $em->persist($game);
            $em->flush();
            return $this->redirectToRoute('game_index');


        }

        return $this->render('game/addExpansion.html.twig', array(
            'game' => $game,
            'form' => $form->createView()
        ));
    }

    public function getAllUserGames()
    {
        /** @var User $user */
        $user = $this->getUser();
        $userGames = $user->getGames();

        return $userGames;

    }

    /**
     * @Route("/is/expansion"), name="validateIfExpansion"
     * @Method({"GET", "POST"})
     *
     */
    public function isExpansion(Request $request)
    {
        $bgg_id = 223555;
        $client = new \Nataniel\BoardGameGeek\Client();
        $thing = $client->getThing($bgg_id, true);
        $bggGame = array(
            array(
                'isExpansion' => $thing->isBoardgameExpansion()
            )
        );
        if ($bggGame[0]['isExpansion'] == 'true') {
            return "true";
        } else {
            return "false";
        }
    }

    /**
     *
     * @Route("/findBy/bggId", name="findGameByBggId")
     * @Method("POST")
     */
    public function getGameByBggId(Request $request)
    {
        $bgg_id = $request->request->get('bggId');
        if (is_numeric($bgg_id)) {
            $client = new \Nataniel\BoardGameGeek\Client();
            /**
             * @var \Nataniel\BoardGameGeek\Client() $thing
             */
            $thing = $client->getThing($bgg_id, true);
            $bggGame = array(
                array(
                    'name' => $thing->getName(),
                    'playtime' => $thing->getPlayingTime(),
                    'image' => $thing->getImage(),
                    'min_players' => $thing->getMinPlayers(),
                    'max_players' => $thing->getMaxPlayers(),
                    'isExpansion' => $thing->isBoardgameExpansion()
                )
            );


            $serializer = $this->get('jms_serializer');
            $data = $serializer->serialize($bggGame, 'json');
            return new Response(
                $data
            );

        } else {
            echo "no valid bgg id";
        }

    }

    /**
     *
     * @Method("GET")
     */
    public function getBggObjectByNameTest()
    {

        $name = 'catan';
        $url = 'https://www.boardgamegeek.com/xmlapi2/search/?query=' . $name;

        $nodes = new SimpleXMLElement(file_get_contents($url));
        $games = array();
        foreach ($nodes->item as $item) {
            array_push($games, $item->name['value']);
        }

        var_dump($games);
        die();
        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize($games, 'json');
        return new Response(
            $data
        );

    }

    /**
     * @Route("/findBy/bggName", name="findGameByName")
     * @Method("GET")
     */
    public function getBggObjectByNameTest2(){
        $client = new Client();
        $results = $client->search('warcraft');
        $things = array();
        foreach($results as $item){
            echo $item->getName();
        }



    }

    public function getAllGames()
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $query = $qb->select('g')
            ->from(Game::class, 'g')
            ->orderBy('g.name', 'asc')
            ->getQuery();

        $games = $query->getResult();
        return $games;
    }

    public function getLatestGamesAction($quantity)
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $query = $qb->select('g')
            ->from(Game::class, 'g')
            ->orderBy('g.id', 'desc')
            ->setMaxResults($quantity)
            ->getQuery();

        $games = $query->getResult();
        return $games;
    }
}
