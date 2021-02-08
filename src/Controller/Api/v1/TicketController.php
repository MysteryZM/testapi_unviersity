<?php

namespace App\Controller\Api\v1;

use App\Entity\FlightTicket;
use App\Entity\User;
use App\Repository\FlightTicketRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    /**
     * @Route("/ticket/add", name="ticket.add")
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Request $request): JsonResponse
    {
        $flightId = (int)$request->get('flight_id');
        $place = (int)$request->get('place');
        $secretKey = $request->get('secret_key');

        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->findOneBy(['secretKey' => $secretKey]);

        /** @var FlightTicketRepository $flightTicketRepo */
        $flightTicketRepo = $this->getDoctrine()->getRepository(FlightTicket::class);
        $flightTicketEnt = $flightTicketRepo->create($flightId, $place, $user);

        return $this->json([
            'status' => true,
            'id' => $flightTicketEnt->getId()
        ]);
    }

    /**
     * @Route("/ticket/reject/{id<\d+>}", name="ticket.reject")
     *
     * @param int $id
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function reject(int $id, Request $request): JsonResponse
    {
        $secretKey = $request->get('secret_key');

        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->findOneBy(['secretKey' => $secretKey]);

        /** @var FlightTicketRepository $flightTicketRepo */
        $flightTicketRepo = $this->getDoctrine()->getRepository(FlightTicket::class);
        $flightTicketEnt = $flightTicketRepo->reject($id, $user);

        return $this->json([
            'status' => true,
            'id' => $flightTicketEnt->getId()
        ]);
    }
}
