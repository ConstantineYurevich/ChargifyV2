<?php
/**
 * ChargifyV2
 *
 * @link      https://github.com/yurevichcv/ChargifyV2
 * @copyright Copyright (c) 2015 Constantine Yurevich
 * @license   https://github.com/yurevichcv/ChargifyV2/blob/master/LICENSE (MIT License)
 */
namespace ChargifyV2;

use GuzzleHttp\ClientInterface;

/**
 * Class Client
 *
 * A wrapper class for Chargify API v2 Resources
 *
 * @link https://docs.chargify.com/api-call
 * @link https://docs.chargify.com/api-signups
 * @link https://docs.chargify.com/api-card-update
 *
 * @package ChargifyV2
 */
class Client
{
    /***
     * @var string
     */
    protected $baseUrl = 'https://api.chargify.com/api/v2';

    /***
     * @var string
     */
    protected $apiId;

    /***
     * @var string
     */
    protected $apiPassword;

    /***
     * @var array
     */
    protected $config;

    /***
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * @param $apiId
     * @param $apiPassword
     * @param null $baseUrl
     */
    public function __construct($apiId, $apiPassword, $baseUrl = null)
    {
        $this->apiId       = $apiId;
        $this->apiPassword = $apiPassword;

        if ($baseUrl !== null) {
            $this->baseUrl = $baseUrl;
        }
    }

    /**
     * @param ClientInterface $httpClient
     */
    public function setHttpClient(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Fetches Chargify Direct call info
     * @link https://docs.chargify.com/api-call
     *
     * @param $callId
     *
     * @return object
     */
    public function getCall($callId)
    {
        $response = $this->request('GET', sprintf('/calls/%s', $callId));
        $result = json_decode($response->getBody()->getContents());

        return $result;
    }

    /**
     * Creates new signup
     * @link https://docs.chargify.com/api-signups
     *
     * @param array $data
     *
     * @return object
     */
    public function signUp(array $data)
    {
        //not implemented yet
    }

    /**
     * Updates a Subscription’s Payment Profile with new information,
     * or creates a new Payment Profile for a Subscription where none currently exists.
     * @link https://docs.chargify.com/api-card-update
     *
     * @param $subscriptionId
     * @param array $data
     *
     * @return object
     */
    public function updateCard($subscriptionId, array $data)
    {
        //not implemented yet
    }

    /**
     * @param string $method    One of GET, POST, PUT, DELETE
     * @param string $path      Chargify API endpoint path
     * @param array $data       Body data which will be sent as json
     * @param array $query      Query string params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function request($method, $path, $data = [], $query = [])
    {
        $method = strtoupper($method);
        $path   = ltrim($path, '/');

        $options['base_uri']    = $this->baseUrl;
        $options['auth']        = [$this->apiId, $this->apiPassword];
        $options['headers']     = [
            'Accept' => 'application/json'
        ];

        if (!empty($query)) {
            $options['query']   = $query;
        }
        if (!empty($data)) {
            $options['json']    = $data;
        }

        $response = $this->getHttpClient()->request($method, $this->baseUrl . '/' . $path, $options);

        return $response;
    }

    /**
     * @return \GuzzleHttp\Client|ClientInterface
     */
    private function getHttpClient()
    {
        if ($this->httpClient === null) {
            $this->httpClient = new \GuzzleHttp\Client();
        }

        return $this->httpClient;
    }
}
