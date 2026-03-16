<?php

namespace App\Tests\Controller;

use App\Controller\WeatherApiController;
use App\Service\WeatherService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

class WeatherApiControllerTest extends TestCase
{
    public static function invalidStationIdProvider(): array
    {
        return [
            'empty string' => [''],
            'contains space' => ['abc def'],
            'only space' => [' '],
            'too long' => [str_repeat('a', 4001)],
        ];
    }

    public function testGetAllStationsReturns200(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $weatherServiceMock = $this->createMock(WeatherService::class);

        $weatherServiceMock
            ->method('getAllStations')
            ->willReturn([
                ['STATION_ID' => 'KALNCIEM', 'NAME' => 'Kalnciems'],
                ['SIGULDA' => 'KALNCIEM', 'NAME' => 'Sigulda'],
            ]);

        $controller = new WeatherApiController($loggerMock, $weatherServiceMock);

        $response = $controller->getAllStations();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetAllStationsReturns500(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $weatherServiceMock = $this->createMock(WeatherService::class);

        $weatherServiceMock
            ->method('getAllStations')
            ->willThrowException(new RuntimeException('API unavailable'));

        $controller = new WeatherApiController($loggerMock, $weatherServiceMock);

        $response = $controller->getAllStations();

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testGetStationInfoReturns200(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $weatherServiceMock = $this->createMock(WeatherService::class);

        $weatherServiceMock
            ->method('getStationInfo')
            ->willReturn([
                ['STATION_ID' => 'KALNCIEM', 'NAME' => 'Kalnciems'] // there are more fields in data.gov but for the mock it doesn't matter
            ]);

        $stationId = 'KALNCIEM';

        $controller = new WeatherApiController($loggerMock, $weatherServiceMock);

        $response = $controller->getStationInfo($stationId);

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[DataProvider('invalidStationIdProvider')]
    public function testGetStationInfoReturns400(string $stationId): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $weatherServiceMock = $this->createMock(WeatherService::class);

        // $weatherServiceMock not needed as only inputs are tested

        $controller = new WeatherApiController($loggerMock, $weatherServiceMock);

        $response = $controller->getStationInfo($stationId);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGetStationInfoReturns404(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $weatherServiceMock = $this->createMock(WeatherService::class);

        $weatherServiceMock
            ->method('getStationInfo')
            ->willReturn([]);

        $controller = new WeatherApiController($loggerMock, $weatherServiceMock);

        $stationId = 'KALNCIEM';

        $response = $controller->getStationInfo($stationId);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetStationInfoReturns500(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $weatherServiceMock = $this->createMock(WeatherService::class);

        $weatherServiceMock
            ->method('getStationInfo')
            ->willThrowException(new RuntimeException('API unavailable'));

        $controller = new WeatherApiController($loggerMock, $weatherServiceMock);

        $stationId = 'KALNCIEM';

        $response = $controller->getStationInfo($stationId);

        $this->assertEquals(500, $response->getStatusCode());
    }
}
