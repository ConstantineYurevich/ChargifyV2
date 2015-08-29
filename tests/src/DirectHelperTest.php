<?php

namespace ChargifyV2\Test;

use ChargifyV2\DirectHelper;

class DirectHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSecureFields()
    {
        $apiId          = 'xxxx';
        $apiSecret      = 'yyyy';
        $redirectUrl    = 'http://example.local';

        $directHelper = new DirectHelper($apiId, $apiSecret, $redirectUrl);

        $directHelper->setData([
            'field1' => 'test1',
            'field2' => 'test2'
        ]);

        $secureFields = $directHelper->getSecureFields();

        $this->assertArrayHasKey('api_id', $secureFields);
        $this->assertArrayHasKey('timestamp', $secureFields);
        $this->assertArrayHasKey('nonce', $secureFields);
        $this->assertArrayHasKey('data', $secureFields);
        $this->assertArrayHasKey('signature', $secureFields);
        $this->assertEquals($apiId, $secureFields['api_id']);
        $this->assertEquals(40, strlen($secureFields['nonce']));
        $this->assertGreaterThanOrEqual(time(), $secureFields['timestamp']);
        $this->assertContains('redirect_uri=' . urlencode($redirectUrl), $secureFields['data']);
        $this->assertEquals(40, strlen($secureFields['signature']));
    }

    public function testGetRequestSignature()
    {
        $apiId          = 'xxxx';
        $apiSecret      = 'yyyy';

        $directHelper = new DirectHelper($apiId, $apiSecret);

        $timeStamp      = $directHelper->getTimeStamp();
        $nonce          = $directHelper->getNonce();
        $dataString     = $directHelper->getDataString();

        $controlSignature = hash_hmac('sha1', $apiId . $timeStamp . $nonce . $dataString, $apiSecret);

        $this->assertEquals($controlSignature, $directHelper->getRequestSignature());
    }

    public function testIsValidResponseSignature()
    {
        $apiId          = 'xxxx';
        $apiSecret      = 'yyyy';
        $redirectUrl    = 'http://example.local';

        $directHelper = new DirectHelper($apiId, $apiSecret, $redirectUrl);

        $timeStamp  = time();
        $nonce      = 'xxx';
        $statusCode = '200';
        $resultCode = '200';
        $callId     = 'zzz';

        $validSignature = hash_hmac('sha1', $apiId . $timeStamp . $nonce . $statusCode . $resultCode . $callId, $apiSecret);
        $notValidSignature = str_repeat('x', 40);

        $this->assertTrue($directHelper->isValidResponseSignature(
            $validSignature, $apiId, $timeStamp, $nonce, $statusCode, $resultCode, $callId
        ));
        $this->assertFalse($directHelper->isValidResponseSignature(
            $notValidSignature, $apiId, $timeStamp, $nonce, $statusCode, $resultCode, $callId
        ));
    }
}