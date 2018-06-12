<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Expansion;
use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Expansion controller.
 *
 * @Route("expansion")
 */
class ExpansionController extends Controller
{

    public function getAllExpansions(){
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $query = $qb->select('exp')
            ->from(Expansion::class, 'exp')
            ->orderBy('exp.name', 'asc')
            ->getQuery();

        $expansions = $query->getResult();
        return $expansions;
    }

    public function getLatestExpansionsAction($quantity)
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $query = $qb->select('exp')
            ->from(Expansion::class, 'exp')
            ->orderBy('exp.id', 'desc')
            ->setMaxResults($quantity)
            ->getQuery();

        $games = $query->getResult();
        return $games;
    }

}