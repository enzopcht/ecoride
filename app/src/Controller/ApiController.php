<?php

namespace App\Controller;

use App\Entity\CarModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class ApiController extends AbstractController
{
    #[Route('/get-models/{brandId}', name: 'get_car_models', methods: ['GET'])]
    public function getCarModels(int $brandId, EntityManagerInterface $em): JsonResponse
    {
        $models = $em->getRepository(CarModel::class)->findBy(['brand' => $brandId]);
    
        $formatted = array_map(function ($model) {
            return [
                'id' => $model->getId(),
                'label' => $model->getLabel(),
            ];
        }, $models);
    
        return $this->json($formatted);
    }

}