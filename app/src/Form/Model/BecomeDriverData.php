<?php 

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Form\Model\VehicleData;


class BecomeDriverData
{
    #[Assert\Valid]
    #[Assert\NotNull]
    public VehicleData $vehicle;

    #[Assert\NotNull]
    public bool $musicAllowed;

    #[Assert\NotNull]
    public bool $smokingAllowed;

    #[Assert\NotNull]
    public bool $animalsAllowed;

    public function getVehicle(): VehicleData
    {
        return $this->vehicle;
    }

    public function setVehicle(VehicleData $vehicle): void
    {
        $this->vehicle = $vehicle;
    }
}