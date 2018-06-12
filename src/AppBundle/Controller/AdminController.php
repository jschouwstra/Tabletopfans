<?php
/**
 * Created by PhpStorm.
 * User: Jelle
 * Date: 29-1-2018
 * Time: 16:49
 */

namespace AppBundle\Controller;

use AppBundle\Repository\GameRepository;
use Nataniel\BoardGameGeek\Exception;
use function range;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\GameController;
use AppBundle\Entity\Expansion;
use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use function is_numeric;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use function var_dump;
use function var_export;


/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/test-sheet")
     */
    public function testSheetAction(Request $request)
    {

        return $this->render('admin/test-sheet.html.twig', array());
    }

    /**
     * @Route("/index")
     */
    public function index(Request $request)
    {
        $gamecontroller = $this->get('gamecontroller');
        $expansioncontroller = $this->get('expansioncontroller');

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/index.html.twig', array(
            'latestGames' => $gamecontroller->getLatestGamesAction(5),
            'latestExpansions' => $expansioncontroller->getLatestExpansionsAction(5),
            'totalExpansions' => count($expansioncontroller->getAllExpansions()),
            'totalGames' => count($gamecontroller->getAllGames())
        ));
    }

    /**
     * @Route("test/giverole")
     */
    public function giveAdminRoleToUser()
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $roles = ["ROLE_ADMIN"];
        $user->setRoles($roles);
        $em = $this->getDoctrine()->getManager();
//        $user->addRole("ROLE_ADMIN");
        $em->persist($user);
        $em->flush();
        return new Response(
            '<html><body>Rol toegevoegd: <br/><pre> ' . var_dump($user) . '</pre></body></html>'
        );
    }

    /**
     * Creates a new game entity.
     *
     * @Route("/game/index", name="admin_game_index")
     * @Method({"GET", "POST"})
     */
    public function gameIndexAction()
    {
//            $gamecontroller = new GameController();
//            $games = $gamecontroller->getAllGames();
//        $games = $this->forward('AppBundle:Game:getAllGamesAction');
//        $games = $this->container->get('app.controller.gamecontroller')->getAllGames();
        $games = $this->get('gamecontroller')->getAllGames();

//        $games = $this->getAllGames();
        return $this->render('admin/game/index.html.twig', array(
            'games' => $games
        ));
    }

    /**
     * Creates a new game entity.
     *
     * @Route("/new-expansion/with-bgg-id", name="expansion_new_with_bgg_id")
     * @Method({"GET", "POST"})
     */
    public function insertNewExpansionWithBggId(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $fill = 'fill()';
        $retrieveExpansionForm = $this->createFormBuilder()
            ->add('bgg_id_to_retrieve', TextType::class
            )
            ->add('isExpansion_to_retrieve', TextType::class, array(
                    'attr' => array(
                        'id' => 'isExpansion_to_retrieve',
                        'label' => 'Expansion',
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->getForm();
        $expansion = new Expansion();

        $form = $this->createFormBuilder($expansion)
            ->add('bgg_id', TextType::class, array(
                    'attr' => array(
                        'id' => 'bgg_id_retrieved',
                        'label' => false,
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->add('name', TextType::class, array(
                    'attr' => array(
                        'id' => 'name',
                        'label' => 'Name',
                        'class' => 'form-control',
                        'readonly' => true

                    ),
                )
            )
            ->add('playtime', TextType::class, array(
                    'attr' => array(
                        'id' => 'playtime',
                        'label' => 'Play time',
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->add('image', TextType::class, array(
                    'attr' => array(
                        'id' => 'image',
                        'label' => 'Image',
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->add('no_of_players', TextType::class, array(
                    'attr' => array(
                        'id' => 'no_of_players',
                        'label' => 'Players',
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->add('isExpansion', TextType::class, array(
                    'attr' => array(
                        'id' => 'bggIsExpansion',
                        'label' => 'Expansion',
                        'class' => 'form-control',
                        'readonly' => true,
                    ),
                )
            )
            ->add('submit', SubmitType::class, array(
                    'attr' => array(
                        'label' => 'Add game',
                        'class' => 'form-control btn btn-success'
                    ),
                )
            )
            ->getForm();
        $retrieveExpansionForm->handleRequest($request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $expansion = $form->getData();
            //Check if it's a game
            try {
                if ($form->get('isExpansion')->getData() == 'true') {

                    $em->persist($expansion);
                    $em->flush();
                    $message = 'Object succesfully added: ' . $form->get('name')->getData();
                    $this->addFlash('success', $message);
                    return $this->redirect($this->generateUrl('expansion_new_with_bgg_id'));
                } else {
                    $this->addFlash('success', 'This is not a expansion.');

                }
            } catch (\Exception $e) {
                $duplicateEntry = '23000';
                if (strpos($e->getMessage(), $duplicateEntry)) {
                    $message = $form->get('name')->getData() . ' already exists.';
                    $this->addFlash('warning', $message);
                }
            }
        }
        return $this->render('admin/expansion/new_expansion_bgg_input.html.twig', array(
            'retrieveGameForm' => $retrieveExpansionForm->createView(),
            'fillGameForm' => $form->createView(),
        ));
    }

    /**
     * Creates a new game entity.
     *
     * @Route("/new-game/with-bgg-id", name="game_new_with_bgg_id")
     * @Method({"GET", "POST"})
     */
    public function insertNewGameWithBggId(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $fill = 'fill()';
        $retrieveGameForm = $this->createFormBuilder()
            ->add('bgg_id_to_retrieve', TextType::class
            )
            ->add('isExpansion_to_retrieve', TextType::class, array(
                    'attr' => array(
                        'id' => 'isExpansion_to_retrieve',
                        'label' => 'Expansion',
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->getForm();
        $game = new Game();

        $form = $this->createFormBuilder($game)
            ->add('bgg_id', TextType::class, array(
                    'attr' => array(
                        'id' => 'bgg_id_retrieved',
                        'label' => false,
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->add('name', TextType::class, array(
                    'attr' => array(
                        'id' => 'name',
                        'label' => 'Name',
                        'class' => 'form-control',
                        'readonly' => true

                    ),
                )
            )
            ->add('playtime', TextType::class, array(
                    'attr' => array(
                        'id' => 'playtime',
                        'label' => 'Play time',
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->add('image', TextType::class, array(
                    'attr' => array(
                        'id' => 'image',
                        'label' => 'Image',
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->add('no_of_players', TextType::class, array(
                    'attr' => array(
                        'id' => 'no_of_players',
                        'label' => 'Players',
                        'class' => 'form-control',
                        'readonly' => true
                    ),
                )
            )
            ->add('isExpansion', TextType::class, array(
                    'attr' => array(
                        'id' => 'bggIsExpansion',
                        'label' => 'Expansion',
                        'class' => 'form-control',
                        'readonly' => true,
                    ),
                )
            )
            ->add('submit', SubmitType::class, array(
                    'attr' => array(
                        'label' => 'Add game',
                        'class' => 'form-control btn btn-success'
                    ),
                )
            )
            ->getForm();
        $retrieveGameForm->handleRequest($request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $game = $form->getData();
            //Check if it's a game
            try {
                if ($form->get('isExpansion')->getData() == 'false') {

                    $em->persist($game);
                    $em->flush();
                    $message = 'Object succesfully added: ' . $form->get('name')->getData();
                    $this->addFlash('success', $message);
                    return $this->redirect($this->generateUrl('game_new_with_bgg_id'));
                } else {
                    $this->addFlash('success', 'This is not a game.');

                }
            } catch (\Exception $e) {
                $duplicateEntry = '23000';
                if (strpos($e->getMessage(), $duplicateEntry)) {
                    $message = $form->get('name')->getData() . ' already exists.';
                    $this->addFlash('warning', $message);
                }
            }
        }
        return $this->render('admin/game/new_game_bgg_input.html.twig', array(
            'retrieveGameForm' => $retrieveGameForm->createView(),
            'fillGameForm' => $form->createView(),
        ));
    }


    /**
     * Creates a new game entity.
     *
     * @Route("admin/new-game", name="game_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $client = new \Nataniel\BoardGameGeek\Client();
        $game = new Game();
        $form = $this->createForm('AppBundle\Form\GameType', $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bgg_id = $form->get('bgg_id')->getData();
            $thing = $client->getThing($bgg_id, true);
            $game->setName($thing->getName());

            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush($game);
            return $this->redirectToRoute('game_show', array('id' => $game->getId()));
        }

        return $this->render('game/new.html.twig', array(
            'game' => $game,
            'form' => $form->createView(),
        ));
    }

    public function isConsecutive($array)
    {
        return ((int)max($array) - (int)min($array) == (count($array) - 1));
    }

    /**
     *
     * @Route("/game-bulk-insert", name="game-bulk-insert")
     * @Method({"GET", "POST"})
     */
    public function gameBulkInsertAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //view
        $bggId = $em->getRepository(Game::class)
            ->getHighestBggId();

        $defaultData = array('range-to' => implode('', $bggId));

        $form = $this->createFormBuilder($defaultData)
            ->add('range-min', TextType::class, array(
                    'label' => 'Minimum range',
                    'attr' => array(
                        'class' => 'form-control'
                    )
                )
            )
            ->add('range-max', TextType::class,
                array(
                    'label' => 'Maximum range',
                    'attr' => array(
                        'class' => 'form-control'
                    ),
                ))
            ->add('submit', SubmitType::class, array(
                'label' => 'confirm',
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Temporary change to script time limit
            set_time_limit(999999999);

            $rangeMin = $form->get('range-min')->getData();
            $rangeMax = $form->get('range-max')->getData();
            $list = array();

            foreach (range($rangeMin, $rangeMax) as $number) {
                array_push($list, $number);
            }

            $listQuantity = count($list);
            $count = 1;
            $bggGamesToBeInserted = array();
            for ($x = 0; $x < $listQuantity; $x++) {
                $exists = $em->getRepository(Game::class)
                    ->bggIdExists($list[$x]);
                if (!$exists) {
                    array_push($bggGamesToBeInserted, $list[$x]);
                }
                $count++;
            }
            foreach ($bggGamesToBeInserted as $bggId) {

                echo $bggId . "<br>";
                $this->newGameByBggId($bggId);
                sleep(2);
            }
            echo "Done";
            // set time limit back to default value
            set_time_limit(120);

        }

        return $this->render('admin/game/insert-bulk.html.twig', array(
            'form' => $form->createView(),
            'allGamesInDataset' => $this->getBggIdNotInDataset()
        ));
    }

    public function newGameByBggId($bgg_id)
    {
        $client = new \Nataniel\BoardGameGeek\Client();
        $thing = $client->getThing($bgg_id, true);
        if (!$thing->isBoardgameExpansion()) {
            if ($thing->getName()) {
                if ($thing->getMinPlayers() > 0) {
                    $game = new Game();

                    $game->setBggId($bgg_id);
                    $game->setName($thing->getName());
                    $game->setNoOfPlayers($thing->getMinPlayers() . "-" . $thing->getMaxPlayers());
                    $game->setPlaytime($thing->getPlayingTime());
                    $game->setImage($thing->getImage());
                    $game->setIsExpansion($thing->isBoardgameExpansion());

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($game);
                    $em->flush();
                }
            }
        } else {
            if ($thing->getName()) {
                if ($thing->getMinPlayers() > 0) {
                    $expansion = new Expansion();

                    $expansion->setBggId($bgg_id);
                    $expansion->setName($thing->getName());
                    $expansion->setNoOfPlayers($thing->getMinPlayers() . "-" . $thing->getMaxPlayers());
                    $expansion->setPlaytime($thing->getPlayingTime());
                    $expansion->setImage($thing->getImage());
                    $expansion->setIsExpansion($thing->isBoardgameExpansion());

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($expansion);
                    $em->flush();
                }
            }
        }
    }

    public function getBggIdNotInDataset()
    {
        //Get all bgg Id's from dataset
        $em = $this->getDoctrine()->getManager();
        $allGamesInDataset = $em->getRepository('AppBundle:Game')->findAllOrderedByBggId();

        // range from 1 to the maximum bgg Id and add to array
        $last = end($allGamesInDataset);
        $range = range(1, $last['bgg_id']);
        $bgglist = array();
        foreach ($allGamesInDataset as $item) {
            array_push($bgglist, $item['bgg_id']);

        }

        //Compare range list
        $missingNumbers = array_diff($range, $bgglist);
        foreach ($missingNumbers as $item) {
            array_push($missingNumbers, $item);
        }

//        return $missingNumbers;
        foreach($missingNumbers as $item){
            echo $item."<br>";
        }
    }


}