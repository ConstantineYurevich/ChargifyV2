<?php

namespace ChargifyV2\Test;

use ChargifyV2\Test\Helper\MockResponseBody;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCallWithSuccessStatus()
    {
        $httpClient = new \GuzzleHttp\Client([
            'handler' => $this->getSuccessResponseMockHandler()
        ]);

        $client = new \ChargifyV2\Client(
            'xxxxx',
            'xxxxx'
        );
        $client->setHttpClient($httpClient);

        $result = $client->getCall('xxxx');

        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('call', $result);
        $this->assertObjectHasAttribute('request', $result->call);
        $this->assertObjectHasAttribute('response', $result->call);
        $this->assertEquals(200, $result->call->response->result->status_code);
    }

    public function testGetCallWithErrorStatus()
    {
        $httpClient = new \GuzzleHttp\Client([
            'handler' => $this->getErrorResposneMockHandler()
        ]);

        $client = new \ChargifyV2\Client(
            'xxxxx',
            'xxxxx'
        );
        $client->setHttpClient($httpClient);

        $result = $client->getCall('xxxx');

        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('call', $result);
        $this->assertObjectHasAttribute('request', $result->call);
        $this->assertObjectHasAttribute('response', $result->call);
        $this->assertEquals(422, $result->call->response->result->status_code);
    }

    private function getSuccessResponseMockHandler()
    {
        $body = MockResponseBody::read('signup.success');

        $mockHandler = new MockHandler([
            new Response(200, [
                'Cache-Control' => 'must-revalidate, private, max-age=0',
                'Content-Type' => 'application/json; charset=utf-8',
                'Date' => 'Fri, 28 Aug 2015 10:35:18 GMT',
                'Etag' => '38042fe852494a1342625cd0cb6f80e5',
                'P3p' => 'CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"',
                'Server' => 'nginx + Phusion Passenger',
                'Status' => '200 OK',
                'Strict-Transport-Security' => 'max-age=31536000',
                'X-Content-Type-Options' => 'nosniff',
                'X-Powered-By' => 'Phusion Passenger',
                'X-Rack-Cache' => 'miss',
                'X-Runtime' => '0.040029',
                'X-Ua-Compatible' => 'IE=Edge,chrome=1',
                'X-Xss-Protection' => '1; mode=block',
                'Content-Length' => strlen($body),
                'Connection' => 'keep-alive'
            ], $body)
        ]);

        return $mockHandler;
    }

    private function getErrorResposneMockHandler()
    {
        $body = MockResponseBody::read('signup.error');

        $mockHandler = new MockHandler([
            new Response(200, [
                'Cache-Control' => 'must-revalidate, private, max-age=0',
                'Content-Type' => 'application/json; charset=utf-8',
                'Date' => 'Fri, 28 Aug 2015 10:35:18 GMT',
                'Etag' => '38042fe852494a1342625cd0cb6f80e5',
                'P3p' => 'CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"',
                'Server' => 'nginx + Phusion Passenger',
                'Status' => '200 OK',
                'Strict-Transport-Security' => 'max-age=31536000',
                'X-Content-Type-Options' => 'nosniff',
                'X-Powered-By' => 'Phusion Passenger',
                'X-Rack-Cache' => 'miss',
                'X-Runtime' => '0.040029',
                'X-Ua-Compatible' => 'IE=Edge,chrome=1',
                'X-Xss-Protection' => '1; mode=block',
                'Content-Length' => strlen($body),
                'Connection' => 'keep-alive'
            ], $body)
        ]);

        return $mockHandler;
    }
}