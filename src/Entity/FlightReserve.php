<?php

namespace App\Entity;

use App\Repository\FlightReserveRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FlightReserveRepository::class)
 */
class FlightReserve
{
    const PLACE_MAX_COUNT = 150;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Flight::class, inversedBy="flightReserves")
     * @ORM\JoinColumn(nullable=false)
     */
    private $flight;

    /**
     * @ORM\Column(type="smallint")
     */
    private $place;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private bool $reject;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="flightReserves")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    public function setFlight(Flight $flight): self
    {
        $this->flight = $flight;

        return $this;
    }

    public function getPlace(): ?int
    {
        return $this->place;
    }

    public function setPlace(int $place): self
    {
        if ($place < 0 || $place > self::PLACE_MAX_COUNT) {
            throw new \InvalidArgumentException(sprintf("Invalid place num. Place num must be 1-%d", self::PLACE_MAX_COUNT));
        }
        $this->place = $place;

        return $this;
    }

    public function getReject(): ?bool
    {
        return $this->reject;
    }

    public function setReject(bool $reject): self
    {
        $this->reject = $reject;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
