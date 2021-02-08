<?php

namespace App\Controller\Api\v1;

use App\Entity\FlightReserve;
use App\Entity\User;
use App\Repository\FlightReserveRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReserveController extends AbstractController
{
    /**
     * @Route("/reserve/add", name="reserve.add")
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

        /** @var FlightReserveRepository $flightReserveRepo */
        $flightReserveRepo = $this->getDoctrine()->getRepository(FlightReserve::class);
        $flightReserveEnt = $flightReserveRepo->create($flightId, $place, $user);

        return $this->json([
            'status' => true,
            'id' => $flightReserveEnt->getId()
        ]);
    }

    /**
     * @Route("/reserve/reject/{id<\d+>}", name="reserve.reject")
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

        /** @var FlightReserveRepository $flightReserveRepo */
        $flightReserveRepo = $this->getDoctrine()->getRepository(FlightReserve::class);
        $flightReserveEnt = $flightReserveRepo->reject($id, $user);

        return $this->json([
            'status' => true,
            'id' => $flightReserveEnt->getId()
        ]);
    }
}
