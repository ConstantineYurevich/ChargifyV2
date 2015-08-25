<?php

namespace ChargifyV2;

/**
 * Class Client
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
     * @var string
     */
    protected $apiSecret;

    /***
     * @var array
     */
    protected $config;

    /***
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /***
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->apiId       = $config['api_id'];
        $this->apiPassword = $config['api_password'];
        $this->apiSecret   = $config['api_secret'];
        $this->baseUrl     = $config['base_url'];

        // set up http client
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => $this->baseUrl,
            'auth' => [$this->apiId, $this->apiPassword]
        ]);
    }

    public function getCall($callId)
    {
        $response = $this->request('GET', sprintf('/calls/%d', $callId));

        //TODO

        return $response;
    }

    private function request($path, $method, $data = [], $query = [])
    {
        $method = strtoupper($method);
        $path   = ltrim($path, '/');

        $headers = ['Accept' => 'application/json'];

        $options = [
            'headers' => $headers
        ];

        if (!empty($query)) {
            $options['query'] = $query;
        }

        if (!empty($data)) {
            $options['json'] = $data;
        }

        $response = $this->httpClient->request($method, $this->baseUrl . '/' . $path, $options);

        return $response;
    }
}
