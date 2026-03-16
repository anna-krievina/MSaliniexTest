<?php
namespace App\Controller;

use App\Service\WeatherService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class WeatherApiController
{
    private WeatherService $weatherService;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, WeatherService $weatherService)
    {
        $this->logger = $logger;
        $this->weatherService = $weatherService;
    }

    #[Route('/api/stations', methods: ['GET'])]
    public function getAllStations(): JsonResponse
    {
        try {
            $stations = $this->weatherService->getAllStations();
            if (count($stations) == 0) {
                return new JsonResponse(['error' => 'Stations not found'], 404);
            }
            return new JsonResponse($stations);
        } catch (Throwable $th) {
            // it didn't log the full content in the console, even though it should be correct, so added error message to the text
            $this->logger->error('Error fetching stations: ' . $th->getMessage(), [
                'exception' => $th
            ]);
            return new JsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    #[Route('/api/stations/{stationId}', methods: ['GET'])]
    public function getStationInfo(string $stationId): JsonResponse
    {
        try {
            // asked ChatGPT about string parameters and their dangers, it's not used for SQL or file paths so this should do
            // I also considered htmlspecialchars and addslashes but in the end, the API seemed to handle SIG'*"ULDA just fine
            // and figured it might break some hypothetical station_id, but added check for whitespace
            // technically the field type of station_id is text which is max 1gb data, but it is set to 4000 per Oracle recommendations and it seems reasonable
            if (strlen($stationId) == 0 || strlen($stationId) > 4000 || str_contains($stationId, ' ')) {
                return new JsonResponse(['error' => 'Invalid station id'], 400);
            }
            $station = $this->weatherService->getStationInfo($stationId);
            if (count($station) == 0) {
                return new JsonResponse(['error' => 'Station not found'], 404);
            }
            return new JsonResponse($station);
        } catch (Throwable $th) {
            // it didn't log the full content in the console, even though it should be correct, so added error message to the text
            $this->logger->error('Error fetching station info: ' . $th->getMessage(), [
                'stationId' => $stationId,
                'exception' => $th
            ]);
            return new JsonResponse(['error' => 'Internal server error'], 500);
        }
    }
}
