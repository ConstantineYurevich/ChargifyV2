<?php
/**
 * ChargifyV2
 *
 * @link      https://github.com/yurevichcv/ChargifyV2
 * @copyright Copyright (c) 2015 Constantine Yurevich
 * @license   https://github.com/yurevichcv/ChargifyV2/blob/master/LICENSE (MIT License)
 */
namespace ChargifyV2;

/**
 * Class DirectHelper
 *
 * Helper class which provides functionality for generating required secure data fields
 * and verifying Chargify Direct signatures
 *
 * @link https://docs.chargify.com/chargify-direct-introduction
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
    protected $redirectUrl;

    /**
     * @var int
     */
    protected $timeStamp;

    /**
     * @var string
     */
    protected $nonce;

    /**
     * @param $apiId
     * @param $apiSecret
     * @param $redirectUrl
     * @param null $baseUrl
     */
    public function __construct($apiId, $apiSecret, $redirectUrl = null, $baseUrl = null)
    {
        $this->apiId        = $apiId;
        $this->apiSecret    = $apiSecret;

        if ($redirectUrl !== null) {
            $this->redirectUrl = $redirectUrl;
            $this->mergeRedirectUri();
        }

        if ($baseUrl !== null) {
            $this->baseUrl = $baseUrl;
        }
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
     * @param $redirectUrl
     * @return $this
     * @throws Exception
     */
    public function setRedirectUrl($redirectUrl)
    {
        if (isset($this->_requestSignature)) {
            throw new Exception('The signature for this request has already been generated.');
        }
        $this->redirectUrl = $redirectUrl;
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
        if (!empty($this->redirectUrl)) {
            $this->data = array_merge_recursive($this->data, array('redirect_uri' => $this->redirectUrl));
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
     * @throws Exception
     */
    public function getSecureFields()
    {
        if ($this->redirectUrl == null) {
            throw new Exception('Redirect URL is not defined. Use setRedirectUrl() method before call');
        }
        return [
            'api_id'    => $this->getApiId(),
            'timestamp' => $this->getTimeStamp(),
            'nonce'     => $this->getNonce(),
            'data'      => $this->getDataStringEncoded(),
            'signature' => $this->getRequestSignature()
        ];
    }
}
