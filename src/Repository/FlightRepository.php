<?php

namespace App\Repository;

use App\Entity\Flight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Flight|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flight|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flight[]    findAll()
 * @method Flight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flight::class);
    }

    /**
     * @param int $flightId
     *
     * @return Flight
     */
    public function findOpenFlight(int $flightId): Flight
    {
        if (empty($flightId)) {
            throw new BadRequestHttpException('Flight id not exist');
        }

        /** @var Flight $flightEnt */
        $flightEnt = $this->find($flightId);

        $this->checkFlightOpen($flightEnt);

        return $flightEnt;
    }

    public function updateState(int $id, int $state): Flight
    {
        $flightEnt = $this->find($id);
        if ($flightEnt !== null) {
            $flightEnt->setState($state);
            $this->_em->persist($flightEnt);
            $this->_em->flush();
        }

        return $flightEnt;
    }

    /**
     * @param Flight|null $flightEnt
     */
    public function checkFlightOpen(?Flight $flightEnt): void
    {
        if ($flightEnt === null) {
            throw new NotFoundHttpException('Flight not found');
        }

        if ($flightEnt->getState() !== Flight::STATE_OPEN) {
            throw new BadRequestHttpException('Flight status is not open');
        }
    }
}
