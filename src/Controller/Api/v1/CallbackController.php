<?php

namespace App\Controller\Api\v1;

use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class CallbackController extends AbstractController
{
    /**
     * @Route("/callback/events", name="callback.events")
     *
     * @param Request $request
     * @param EventService $eventService
     *
     * @return Response
     */
    public function events(Request $request, EventService $eventService): Response
    {
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer([], $encoders);
        $content = $request->getContent();
        $content = $serializer->decode($content, 'json');
        $data = $content['data'];

        $eventService->__call($data['event'], $data);

        return $this->json(['status' => true]);
    }
}
