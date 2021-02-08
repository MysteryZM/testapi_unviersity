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
 * @method FlightTicket|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlightTicket|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlightTicket[]    findAll()
 * @method FlightTicket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightTicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlightTicket::class);
    }

    /**
     * @param int $flightId
     * @param int $place
     * @param User|null $user
     *
     * @return FlightTicket
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(int $flightId, int $place, ?User $user): FlightTicket
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
     * @return FlightTicket
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function reject(int $id, ?User $user): FlightTicket
    {

        /** @var FlightTicket $flightTicketEnt */
        $flightTicketEnt = $this->findOneBy(['id' => $id, 'customer' => $user]);

        $this->checkReject($flightTicketEnt);

        $flightTicketEnt->setReject(true);

        $this->_em->persist($flightTicketEnt);
        $this->_em->flush();

        return $flightTicketEnt;
    }

    /**
     * @param Flight $flightEnt
     * @param int $place
     * @param User|null $user
     *
     * @return FlightTicket
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Flight $flightEnt, int $place, ?User $user): FlightTicket
    {
        $flightTicketEnt = new FlightTicket();
        $flightTicketEnt->setFlight($flightEnt);
        $flightTicketEnt->setPlace($place);
        $flightTicketEnt->setReject(false);
        $flightTicketEnt->setCustomer($user);

        $this->_em->persist($flightTicketEnt);
        $this->_em->flush();

        return $flightTicketEnt;
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

        $flightTicketEnt = $this->findBy(['flight' => $flightEnt, 'place' => $place, 'reject' => false]);

        /** @var FlightReserveRepository $flightRepo */
        $flightReserveRepo = $this->_em->getRepository(FlightReserve::class);
        $flightReserveEnt = $flightReserveRepo->findBy(['flight' => $flightEnt, 'place' => $place, 'reject' => false]);

        if (!empty($flightReserveEnt) || !empty($flightTicketEnt)) {
            throw new ConflictHttpException('This place is already occupied');
        }
    }

    /**
     * @param FlightTicket|null $flightTicketEnt
     */
    public function checkReject(?FlightTicket $flightTicketEnt): void
    {
        if ($flightTicketEnt === null) {
            throw new NotFoundHttpException('Flight not found');
        }

        if ($flightTicketEnt->getReject()) {
            throw new ConflictHttpException('Flight ticket is already rejected');
        }
    }
}
