<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DataGovApiService
{    private HttpClientInterface $client;
    private string $dataGovApiUrl;

    public function __construct(HttpClientInterface $client,
                                string $dataGovApiUrl)
    {
        $this->client = $client;
        // configured in services.yaml
        $this->dataGovApiUrl = $dataGovApiUrl;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    // API reference: https://docs.ckan.org/en/latest/maintaining/datastore.html#api-reference
    public function dataGovAPIDatastoreSearch(string $resourceId, array $fields = [], array $query = []): array
    {
        // wrote '/datastore_search' in case datastore_search_sql is needed
        $response = $this->client->request('POST', $this->dataGovApiUrl . '/datastore_search',
            [
                // Public resources require no authorization
//                'headers' => [
//                    'Authorization' => $this->apiToken
//                ],
                'json' => [
                    'resource_id' => $resourceId,
                    'limit' => 32000, // upper limit, per API description
                    'fields' => $fields,
                    'q' => count($query) > 0 ? $query : null // has to be null for the query to be ignored
                ]
            ]
        );

        return $response->toArray();
    }
}
