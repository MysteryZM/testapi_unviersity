<?php

namespace App\Repository;

use App\Entity\Flight;
use App\Entity\FlightReserve;
use App\Entity\FlightTicket;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method FlightReserve|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlightReserve|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlightReserve[]    findAll()
 * @method FlightReserve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightReserveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlightReserve::class);
    }

    /**
     * @param int $flightId
     * @param int $place
     * @param User|null $user
     *
     * @return FlightReserve
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(int $flightId, int $place, ?User $user): FlightReserve
    {
        /** @var FlightRepository $flightRepo */
        $flightRepo = $this->_em->getRepository(Flight::class);
        $flightEnt = $flightRepo->findOpenFlight($flightId);

        $this->checkOccupied($flightEnt, $place);

        return $this->add($flightEnt, $place, $user);
    }

    /**
     * @param int $id
     * @param User|null $user
     *
     * @return FlightReserve
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function reject(int $id, ?User $user): FlightReserve
    {
        /** @var FlightReserve $flightReserveEnt */
        $flightReserveEnt = $this->findOneBy(['id' => $id, 'customer' => $user]);

        $this->checkReject($flightReserveEnt);

        $flightReserveEnt->setReject(true);

        $this->_em->persist($flightReserveEnt);
        $this->_em->flush();

        return $flightReserveEnt;
    }

    /**
     * @param Flight $flightEnt
     * @param int $place
     * @param User|null $user
     *
     * @return FlightReserve
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Flight $flightEnt, int $place, ?User $user): FlightReserve
    {
        $flightReserveEnt = new FlightReserve();
        $flightReserveEnt->setFlight($flightEnt);
        $flightReserveEnt->setPlace($place);
        $flightReserveEnt->setReject(false);
        $flightReserveEnt->setCustomer($user);

        $this->_em->persist($flightReserveEnt);
        $this->_em->flush();

        return $flightReserveEnt;
    }

    /**
     * @param Flight $flightEnt
     * @param int $place
     */
    public function checkOccupied(Flight $flightEnt, int $place): void
    {
        if (empty($place)) {
            throw new BadRequestHttpException('Place num not exist');
        }

        $flightReserveEnt = $this->findBy(['flight' => $flightEnt, 'place' => $place, 'reject' => false]);

        /** @var FlightTicketRepository $flightRepo */
        $flightTicketRepo = $this->_em->getRepository(FlightTicket::class);
        $flightTicketEnt = $flightTicketRepo->findBy(['flight' => $flightEnt, 'place' => $place, 'reject' => false]);

        if (!empty($flightReserveEnt) || !empty($flightTicketEnt)) {
            throw new ConflictHttpException('This place is already occupied');
        }
    }

    /**
     * @param FlightReserve|null $flightReserveEnt
     */
    public function checkReject(?FlightReserve $flightReserveEnt): void
    {
        if ($flightReserveEnt === null) {
            throw new NotFoundHttpException('Flight not found');
        }

        if ($flightReserveEnt->getReject()) {
            throw new ConflictHttpException('Flight reserve is already rejected');
        }
    }
}
