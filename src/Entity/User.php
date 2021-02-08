<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=FlightReserve::class, mappedBy="customer", orphanRemoval=true)
     */
    private $flightReserves;

    /**
     * @ORM\OneToMany(targetEntity=FlightTicket::class, mappedBy="customer", orphanRemoval=true)
     */
    private $flightTickets;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $secretKey;

    public function __construct()
    {
        $this->flightReserves = new ArrayCollection();
        $this->flightTickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|FlightReserve[]
     */
    public function getFlightReserves(): Collection
    {
        return $this->flightReserves;
    }

    public function addFlightReserve(FlightReserve $flightReserve): self
    {
        if (!$this->flightReserves->contains($flightReserve)) {
            $this->flightReserves[] = $flightReserve;
            $flightReserve->setCustomer($this);
        }

        return $this;
    }

    public function removeFlightReserve(FlightReserve $flightReserve): self
    {
        if ($this->flightReserves->removeElement($flightReserve)) {
            // set the owning side to null (unless already changed)
            if ($flightReserve->getCustomer() === $this) {
                $flightReserve->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FlightTicket[]
     */
    public function getFlightTickets(): Collection
    {
        return $this->flightTickets;
    }

    public function addFlightTicket(FlightTicket $flightTicket): self
    {
        if (!$this->flightTickets->contains($flightTicket)) {
            $this->flightTickets[] = $flightTicket;
            $flightTicket->setCustomer($this);
        }

        return $this;
    }

    public function removeFlightTicket(FlightTicket $flightTicket): self
    {
        if ($this->flightTickets->removeElement($flightTicket)) {
            // set the owning side to null (unless already changed)
            if ($flightTicket->getCustomer() === $this) {
                $flightTicket->setCustomer(null);
            }
        }

        return $this;
    }

    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    public function setSecretKey(string $secretKey): self
    {
        $this->secretKey = $secretKey;

        return $this;
    }
}
