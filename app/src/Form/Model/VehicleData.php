<?php

namespace App\Form\Model;

use App\Entity\CarModel;
use App\Entity\CarBrand;
use Symfony\Component\Validator\Constraints as Assert;

class VehicleData
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[A-HJ-NP-TV-Z]{2}[\s-]?[0-9]{3}[\s-]?[A-HJ-NP-TV-Z]{2}$/',
        message: 'Le format doit être de type AA-123-AA'
    )]
    public string $plate;

    #[Assert\NotBlank]
    #[Assert\LessThanOrEqual('today', message: 'La date d\'immatriculation ne peut être supérieur à ce jour.')]
    public \DateTimeInterface $firstRegistrationDate;

    #[Assert\NotBlank]
    public ?CarModel $model = null;

    #[Assert\NotBlank]
    public ?CarBrand $brand = null;

    #[Assert\NotBlank]
    public string $color;

    public function getModel(): ?CarModel
    {
        return $this->model;
    }

    public function setModel(?CarModel $model): void
    {
        $this->model = $model;
    }
}
