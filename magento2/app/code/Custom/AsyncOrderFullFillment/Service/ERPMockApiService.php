<?php

/**
 *  @author Syed Aun Abbas <syedaun.abbasrizvi@gmail.com>
 */

namespace Custom\AsyncOrderFullFillment\Service;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class ERPMockApiService
 */
class ERPMockApiService
{
    /**
     * ERP MOCK API request URL
     */
    const API_REQUEST_URI = 'https://62936c8d089f87a57ac00d1b.mockapi.io/api/v1/';

    /**
     * API request endpoint
     */
    const API_REQUEST_ENDPOINT = 'orders';

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * ERPMockApiService constructor
     *
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory
    ) {
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Fetch some data from API
     */
    public function execute(): void
    {
        $response = $this->doRequest(static::API_REQUEST_ENDPOINT);
        $status = $response->getStatusCode(); // 200 status code
        $responseBody = $response->getBody();
        $responseContent = $responseBody->getContents(); // here you will have the API response in JSON format
        // Add your logic using $responseContent
    }


    /**
     * Sync/Store order data
     *
     * @param array $order
     */
    public function store(array $order)
    {
        $params = [
            'headers' => [
                'Accept' => 'application/json',
                'content-type' => 'application/json'
            ],
            'json' => $order
        ];
        
        return $response = $this->doRequest(static::API_REQUEST_ENDPOINT, $params, Request::HTTP_METHOD_POST);
    }

    /**
     * Do API request with provided params
     *
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     *
     * @return Response
     */
    private function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response {
        /** @var Client $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params
            );
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }
}
