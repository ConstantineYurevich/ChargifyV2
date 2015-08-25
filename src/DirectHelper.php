<?php

namespace ChargifyV2;

/**
 * Class DirectHelper
 *
 * @package ChargifyV2
 */
class DirectHelper
{
    /***
     * @var string
     */
    protected $baseUrl = 'https://api.chargify.com/api/v2';

    /***
     * @var string
     */
    protected $apiId;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $requestSignature;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var int
     */
    protected $timeStamp;

    /**
     * @var string
     */
    protected $nonce;

    public function __construct(array $config)
    {
        $this->apiId        = $config['api_id'];
        $this->apiSecret    = $config['api_secret'];
        $this->baseUrl      = $config['base_url'];
    }

    /**
     * @return string
     */
    public function getApiId()
    {
        return $this->apiId;
    }

    /**
     * @return string
     */
    protected function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Tamper-proof data that you want to send to Chargify
     * @link https://docs.chargify.com/chargify-direct-introduction#secure-data
     *
     * @param array $data
     * @throws Exception
     * @return $this
     */
    public function setData(array $data = [])
    {
        if (isset($this->requestSignature)) {
            throw new Exception('The signature for this request has already been generated.');
        }
        $this->data = $data;
        $this->mergeRedirectUri();

        return $this;
    }


    /**
     * Get string value to send in secure[data]
     *
     * This is the query string equivalent of $this->data generated with http_build_query()
     *
     * @return string
     */
    public function getDataString()
    {
        $string = http_build_query($this->data);
        $string = str_replace(array('%5B', '%5D'), array('[', ']'), $string);

        return $string;
    }

    /**
     * Get string value to send in secure[data] using &amp; as the arg separator
     *
     * @return string
     */
    public function getDataStringEncoded()
    {
        // percent encoded
        $string = http_build_query($this->data, '', '&amp;');
        $string = str_replace(array('%5B', '%5D'), array('[', ']'), $string);

        return $string;
    }

    /**
     * Set the URI where Chargify should redirect
     *
     * @param $redirectUri
     * @return $this
     * @throws Exception
     */
    public function setRedirectUri($redirectUri)
    {
        if (isset($this->_requestSignature)) {
            throw new Exception('The signature for this request has already been generated.');
        }
        $this->redirectUri = $redirectUri;
        $this->mergeRedirectUri();

        return $this;
    }

    /**
     * The redirect_uri must be sent with secure[data]
     *
     * @return void
     */
    protected function mergeRedirectUri()
    {
        if (!empty($this->redirectUri)) {
            $this->data = array_merge_recursive($this->data, array('redirect_uri' => $this->redirectUri));
        }
    }

    /**
     * Get a Unix timestamp
     *
     * @return int
     */
    public function getTimeStamp()
    {
        if (empty($this->timeStamp)) {
            $this->timeStamp = time();
        }

        return $this->timeStamp;
    }

    /**
     * Get a 40 character string to use as a nonce
     *
     * @return string
     */
    public function getNonce()
    {
        if (empty($this->nonce)) {
            // generate a random string
            $bits   = 256;
            $bytes  = ceil($bits / 8);
            $string = '';
            for ($i = 0; $i < $bytes; $i++) {
                $string .= chr(mt_rand(0, 255));
            }
            $this->nonce = hash('sha1', $string);
        }

        return $this->nonce;
    }

    /**
     * Calculates the hmac-sha1 signature of the request
     *
     * This will be sent as secure[signature] in the request
     *
     * @return string
     */
    public function getRequestSignature()
    {
        if (empty($this->requestSignature)) {
            $string = $this->getApiId() . $this->getTimeStamp() . $this->getNonce() . $this->getDataString();
            $this->requestSignature = hash_hmac('sha1', $string, $this->getApiSecret());
        }

        return $this->requestSignature;
    }

    /**
     * Calculates the hmac-sha1 signature of the response
     *
     * @param $apiId
     * @param $timeStamp
     * @param $nonce
     * @param $statusCode
     * @param $resultCode
     * @param $callId
     *
     * @return string
     */
    protected function getResponseSignature($apiId, $timeStamp, $nonce, $statusCode, $resultCode, $callId)
    {
        $string = $apiId . $timeStamp . $nonce . $statusCode . $resultCode . $callId;

        return hash_hmac('sha1', $string, $this->getApiSecret());
    }

    /**
     * Test if response signature is valid
     *
     * This should be called after the redirect from Chargify to verify the
     * response signature.
     *
     * @param $signature
     * @param $apiId
     * @param $timeStamp
     * @param $nonce
     * @param $statusCode
     * @param $resultCode
     * @param $callId
     *
     * @return bool
     */
    public function isValidResponseSignature($signature, $apiId, $timeStamp, $nonce, $statusCode, $resultCode, $callId)
    {
        $validSignature = $this->getResponseSignature($apiId, $timeStamp, $nonce, $statusCode, $resultCode, $callId);
        return ($signature == $validSignature);
    }

    /**
     * The <form action=""> to use for signups
     *
     * @return string
     */
    public function getSignUpAction()
    {
        return $this->getBaseUrl() . '/signups';
    }

    /**
     * The <form action=""> to use for credit card updates
     *
     * @param int $subscriptionId The ID of the subscription you want to update
     *
     * @return string
     */
    public function getCardUpdateAction($subscriptionId)
    {
        return $this->getBaseUrl() . sprintf('/subscriptions/%d/card_update', $subscriptionId);
    }

    /**
     * Get array of all hidden fields
     *
     * @return array
     */
    public function getSecureFields()
    {
        return [
            'api_id'    => $this->getApiId(),
            'timestamp' => $this->getTimeStamp(),
            'nonce'     => $this->getNonce(),
            'data'      => $this->getDataStringEncoded(),
            'signature' => $this->getRequestSignature()
        ];
    }
}
