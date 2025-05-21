<?php

namespace App\Entity;

use App\Repository\RideRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RideRepository::class)]
class Ride
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $departure_city = null;

    #[ORM\Column(length: 255)]
    private ?string $departure_address = null;

    #[ORM\Column(length: 10)]
    private ?string $departure_post_code = null;

    #[ORM\Column(length: 255)]
    private ?string $arrival_city = null;

    #[ORM\Column(length: 255)]
    private ?string $arrival_address = null;

    #[ORM\Column(length: 10)]
    private ?string $arrival_post_code = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $departure_time = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $arrival_time = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?int $seats_available = null;

    #[ORM\Column]
    private ?bool $ecological = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $driver = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepartureCity(): ?string
    {
        return $this->departure_city;
    }

    public function setDepartureCity(string $departure): static
    {
        $this->departure_city = $departure;

        return $this;
    }
    
    public function getDepartureAddress(): ?string
    {
        return $this->departure_address;
    }

    public function setDepartureAddress(string $departure): static
    {
        $this->departure_address = $departure;

        return $this;
    }

    public function getArrivalCity(): ?string
    {
        return $this->arrival_city;
    }

    public function setArrivalCity(string $arrival): static
    {
        $this->arrival_city = $arrival;

        return $this;
    }

public function getArrivalAddress(): ?string
    {
        return $this->arrival_address;
    }

public function setArrivalAddress(string $arrival): static
    {
        $this->arrival_address = $arrival;

        return $this;
    }

    public function getDepartureTime(): ?\DateTimeImmutable
    {
        return $this->departure_time;
    }

    public function setDepartureTime(\DateTimeImmutable $departure_time): static
    {
        $this->departure_time = $departure_time;

        return $this;
    }

    public function getArrivalTime(): ?\DateTimeImmutable
    {
        return $this->arrival_time;
    }

    public function setArrivalTime(\DateTimeImmutable $arrival_time): static
    {
        $this->arrival_time = $arrival_time;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSeatsAvailable(): ?int
    {
        return $this->seats_available;
    }

    public function setSeatsAvailable(int $seats_available): static
    {
        $this->seats_available = $seats_available;

        return $this;
    }

    public function isEcological(): ?bool
    {
        return $this->ecological;
    }

    public function setEcological(bool $ecological): static
    {
        $this->ecological = $ecological;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDriver(): ?User
    {
        return $this->driver;
    }

    public function setDriver(?User $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getDeparturePostCode(): ?string
    {
        return $this->departure_post_code;
    }
    
    public function setDeparturePostCode(string $departure_post_code): static
    {
        $this->departure_post_code = $departure_post_code;
    
        return $this;
    }

    public function getArrivalPostCode(): ?string
    {
        return $this->arrival_post_code;
    }
    
    public function setArrivalPostCode(string $arrival_post_code): static
    {
        $this->arrival_post_code = $arrival_post_code;
    
        return $this;
    }
}
