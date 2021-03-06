<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use function var_dump;

/**
 * GameRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GameRepository extends EntityRepository
{

    public function findAllOrdered()
    {


        // test if method is called

//        die("findAllOrdered works!");

        ///////////////////////
        //dql get all games:
        ///////////////////////

        //$dql = 'SELECT game FROM AppBundle:Game game ORDER BY game.name ASC';
        //$query = $this->getEntityManager()->createQuery($dql);


        ///////////////////////
        //Query builder:
        ///////////////////////

        $fields = array('n.name');
        $query = $this->getEntityManager()->createQueryBuilder();

        $query
            ->select($fields)
            ->from('AppBundle:Game', 'n');

        $results = $query->getQuery()->getResult();


        return $results;


    }

    public function findByName($name)
    {
        $fields = array('n.name');
        $query = $this->getEntityManager()->createQueryBuilder();

        $query
            ->select($fields)
            ->from('AppBundle:Game', 'n')
            ->where('n.name LIKE :name')
            ->setParameter('name', '%' . $name . '%');
        $results = $query->getQuery()->getResult();


        return $results;

    }

    public function findAllForUser($user_id)
    {
        // test if method is called

//        die("findAllOrdered works!");

        ///////////////////////
        //Query builder:
        ///////////////////////

//        $qb = $this->createQueryBuilder('game')
//            ->addOrderBy('game.name', 'ASC')
//            ->andWhere('game.user_id = :user')
//
//            ->setParameter('user', $user_id);
//        ;

        //        $em = $this->getDoctrine()->getManager();
//

        $qb = $this->em->getRepository(Game::class)->createQueryBuilder('game_t');
        $qb->join('users_games.user_id', 'ug')
            ->where('ug.user_id = :userId')
            ->setParameter('userId', $user_id);
        return $qb->getQuery()->getResult();


    }

    public function getHighestBggId()
    {
        $fields = array('game.bgg_id');
        $query = $this->getEntityManager()->createQueryBuilder();

        $query
            ->select($fields)
            ->from('AppBundle:Game', 'game')
            ->addOrderBy('game.bgg_id', 'DESC')
            ->setMaxResults(1);
        $results = $query->getQuery()->getResult();


        return $results[0];
    }


    public function bggIdExists($bgg_id)
    {
        $fields = array('game.bgg_id');
        $query = $this->getEntityManager()->createQueryBuilder();

        $query
            ->select($fields)
            ->from('AppBundle:Game', 'game')
            ->andWhere('game.bgg_id =' . $bgg_id);

//        var_dump($query->getQuery());

        $results = $query->getQuery()->getResult();
        if (count($results) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function findAllOrderedByBggId()
    {
        $fields = array('bgg_id.bgg_id');
        $query = $this->getEntityManager()->createQueryBuilder();

        $query
            ->select($fields)
            ->from('AppBundle:Game', 'bgg_id');

        $results = $query->getQuery()->getResult();


        return $results;


    }


}
