<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class WeatherApiController
{
    #[Route('/api/stations', methods: ['GET'])]
    public function getAllStations(): JsonResponse
    {
        return new JsonResponse(['message' => 'allStations']);
    }
    #[Route('/api/stations/{stationId}', methods: ['GET'])]
    public function getStationInfo(string $stationId): JsonResponse
    {
        return new JsonResponse(['message' => 'getStationInfo' . $stationId]);
    }
}
