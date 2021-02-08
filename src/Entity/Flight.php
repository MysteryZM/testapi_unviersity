<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FlightRepository::class)
 */
class Flight
{
    const STATE_CREATED = 1;
    const STATE_OPEN = 2;
    const STATE_CLOSED = 3;
    const STATE_CANCELED = 4;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity=FlightReserve::class, mappedBy="flight", orphanRemoval=true)
     */
    private $reserves;

    /**
     * @ORM\OneToMany(targetEntity=FlightTicket::class, mappedBy="flight", orphanRemoval=true)
     */
    private $tickets;

    public function __construct()
    {
        $this->reserves = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        if (!in_array($state, [self::STATE_CREATED, self::STATE_OPEN, self::STATE_CLOSED, self::STATE_CANCELED])) {
            throw new \InvalidArgumentException("Invalid flight state");
        }
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection|FlightReserve[]
     */
    public function getReserves(): Collection
    {
        return $this->reserves;
    }

    public function addReserve(FlightReserve $reserve): self
    {
        if (!$this->reserves->contains($reserve)) {
            $this->reserves[] = $reserve;
            $reserve->setFlight($this);
        }

        return $this;
    }

    public function removeReserve(FlightReserve $reserve): self
    {
        if ($this->reserves->removeElement($reserve)) {
            // set the owning side to null (unless already changed)
            if ($reserve->getFlight() === $this) {
                $reserve->setFlight(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FlightTicket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(FlightTicket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setFlight($this);
        }

        return $this;
    }

    public function removeTicket(FlightTicket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getFlight() === $this) {
                $ticket->setFlight(null);
            }
        }

        return $this;
    }
}
