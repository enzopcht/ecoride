<?php 

namespace App\Form\Model;

use App\Entity\CarModel;
use Symfony\Component\Validator\Constraints as Assert;


class BecomeDriverData
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[A-Z]{2}-\d{3}-[A-Z]{2}$/',
        message: 'Le format doit être de type AA-123-AA'
    )]
    public string $plate;

    #[Assert\NotBlank]
    public \DateTimeInterface $firstRegistrationDate;

    #[Assert\NotBlank]
    public CarModel $model;

    #[Assert\NotBlank]
    public string $brand;

    #[Assert\NotBlank]
    public string $color;

    #[Assert\NotNull]
    public bool $musicAllowed;

    #[Assert\NotNull]
    public bool $smokingAllowed;

    #[Assert\NotNull]
    public bool $animalsAllowed;
}