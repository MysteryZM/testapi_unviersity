<?php

namespace App\Service;


use App\Entity\Flight;
use App\Entity\FlightReserve;
use App\Entity\FlightTicket;
use App\Message\SendEmailMessage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EventService
{
    public const EVENT_LIST = [
        'flight_ticket_sales_completed',
        'flight_reject',
    ];

    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, MessageBusInterface $bus)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->bus = $bus;
    }

    public function __call(string $name, ?array $args): void
    {
        $methods = get_class_methods($this);
        $nameMethod = str_replace('_', '', lcfirst(ucwords($name, '_')));
        if (!in_array($nameMethod, $methods, true) || !in_array($name, self::EVENT_LIST)) {
            throw new \BadMethodCallException("Event {$name} not exist.");
        }

        $this->$nameMethod($args);
    }

    /**
     * @param array $args
     */
    private function flightTicketSalesCompleted(array $args): void
    {
        $this->_updateFlightState($args['flight_id'], Flight::STATE_CLOSED, 'продажа билетов завершена');
    }

    /**
     * @param array $args
     */
    private function flightReject(array $args): void
    {
        $this->_updateFlightState($args['flight_id'], Flight::STATE_CANCELED, 'рейс отменен');
    }

    private function _updateFlightState(int $flightId, int $state, ?string $message = null): void
    {
        /** @var Flight $flightEnt */
        $flightEnt = $this->em->find(Flight::class, $flightId);

        if ($flightEnt !== null) {
            if ($message !== null && $flightEnt->getState() !== $state) {
                $notifiedUser = [];
                /** @var FlightReserve $item */
                foreach ($flightEnt->getReserves()->toArray() as $item) {
                    $user = $item->getCustomer();
                    if (isset($user) && !isset($notifiedUser[$user->getId()])) {
                        $this->_notifyEmail(
                            $user->getEmail(),
                            'sasulka1512@gmail.com',
                            'УВЕДОМЛЕНИЕ',
                            $message
                        );
                        $notifiedUser[$user->getId()] = true;
                    }
                }
                /** @var FlightTicket $item */
                foreach ($flightEnt->getTickets()->toArray() as $item) {
                    $user = $item->getCustomer();
                    if (!isset($notifiedUser[$user->getId()])) {
                        $this->_notifyEmail(
                            $user->getEmail(),
                            'sasulka1512@gmail.com',
                            'УВЕДОМЛЕНИЕ',
                            $message
                        );
                        $notifiedUser[$user->getId()] = true;
                    }
                }
            }


            $flightEnt->setState($state);
            $this->em->persist($flightEnt);
            $this->em->flush();
        } else {
            $dateTime = new \DateTime();
            $this->logger->error(sprintf('%s %s FLIGHT NOT FOUND', $dateTime->format("y:m:d h:i:s"), __METHOD__));
        }
    }

    private function _notifyEmail(string $emailTo, string $emailFrom, string $title, string $content): void
    {
        $this->bus->dispatch(new SendEmailMessage($emailTo, $emailFrom, $title, $content));
    }
}