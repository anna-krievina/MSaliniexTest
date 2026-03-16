<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class WeatherService
{
    private DataGovApiService $dataGovApiService;
    private string $resourceId;

    public function __construct(DataGovApiService $dataGovApiService,
                                string $resourceId)
    {
        $this->dataGovApiService = $dataGovApiService;
        // configured in services.yaml
        $this->resourceId = $resourceId;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getAllStations(): array
    {
        $fields = ['STATION_ID', 'NAME'];
        $result = $this->dataGovApiService->DataGovAPIDatastoreSearch($this->resourceId, $fields);
        return $result['result']['records'] ?? [];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getStationInfo(string $stationId): array {
        $query = [
            'STATION_ID' => $stationId
        ];
        $result = $this->dataGovApiService->DataGovAPIDatastoreSearch($this->resourceId, [], $query);
        return $result['result']['records'] ?? [];
    }
}
