<?php

namespace Renegare\GuzzleClientHelper\Test;

use GuzzleHttp\Exception\ClientException;

class GuzzlerTest extends \PHPUnit_Framework_TestCase {
    use \Renegare\GuzzleClientHelper\GuzzlerTestTrait;

    public function testGuzzler() {
        $client = $this->getMockForAbstractClass('Renegare\GuzzleClientHelper\AbstractClient', ['http://api.example.com', $this->getMock('Psr\Log\LoggerInterface')]);
        $guzzleClient = $client->getGuzzle();
        $this->assertInstanceOf('GuzzleHttp\Client', $guzzleClient);
        $this->assertEquals('http://api.example.com', $guzzleClient->getBaseUrl());
    }

    public function testGuzzleRequestCanBeAsserted() {
        $client = $this->getMockForAbstractClass('Renegare\GuzzleClientHelper\AbstractClient', ['https://api.example.com', $this->getMock('Psr\Log\LoggerInterface')]);

        // make assertion on request attributes
        $this->assertRequest($client, [
            'method' => 'GET',
            'path' => '/resource',
            'query' => ['param' => 'value'],
            'headers' => ['X-Param' => 'header-value'],
            'body' => http_build_query(['field' => 'value'])
        ]);

        $response = $client->getGuzzle()->get('/resource', [
            'query' => ['param' => 'value'],
            'headers' => ['X-Param' => 'header-value'],
            'body' => ['field' => 'value']
        ]);
        $this->assertInstanceOf('GuzzleHttp\Message\ResponseInterface', $response);
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testGuzzleResponseCanBeMocked() {
        $client = $this->getMockForAbstractClass('Renegare\GuzzleClientHelper\AbstractClient', ['https://api.example.com', $this->getMock('Psr\Log\LoggerInterface')]);

        // mock with string
        $responseMessage = <<<EOF
HTTP/1.1 200 OK
Content-Type: application/json
Date: Thu, 04 Sep 2014 16:53:13 GMT

{"some": "data"}
EOF
;
        $this->assertRequest($client, [], $responseMessage);
        $response = $client->getGuzzle()->get('https://api.example.com/resource', []);
        $this->assertEquals($responseMessage, preg_replace('/\r/', '', (string) $response));

        // mock with response object
        $responseMessage = $this->createMockResponse(<<<EOF
HTTP/1.1 200 OK
Content-Type: application/json
Date: Thu, 04 Sep 2014 16:53:13 GMT

{"some": "data"}
EOF
);
        $this->assertRequest($client, [], $responseMessage);
        $response = $client->getGuzzle()->get('https://api.example.com/resource', []);
        $this->assertNotSame($responseMessage, $response);
        $this->assertEquals((string) $responseMessage, (string) $response);

        // mock  with array given json contype[d] request
        $jsonResponse = ['some' => 'data'];
        $this->assertRequest($client, [], $jsonResponse);
        $response = $client->getGuzzle()->get('https://api.example.com/resource', [
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $this->assertEquals($jsonResponse, $response->json());

    }

    public function testMockedClientExceptionIsThrownFor4XX() {
        $client = $this->getMockForAbstractClass('Renegare\GuzzleClientHelper\AbstractClient', ['https://api.example.com', $this->getMock('Psr\Log\LoggerInterface')]);
        try {
            $badResponse = <<<EOF
HTTP/1.1 400 Bad Request
Content-Type: application/json
Date: Thu, 04 Sep 2014 16:53:13 GMT

{"error": "something went wrong"}
EOF
;
            $this->assertRequest($client, [], function($request) use ($badResponse) {
                return $this->createMockResponse($badResponse);
            });

            $client->getGuzzle()->get('https://api.example.com/resource', []);
            $this->assertTrue(false, 'An exception should have been thrown before reaching this point!!!');
        } catch(ClientException $e) {
            $this->assertTrue($e->hasResponse());
            $this->assertEquals($badResponse, preg_replace('/\r/', '', (string) $e->getResponse()));
        }
    }

    public function testMockedClientExceptionIsThrownFor5XX() {
        $client = $this->getMockForAbstractClass('Renegare\GuzzleClientHelper\AbstractClient', ['https://api.example.com', $this->getMock('Psr\Log\LoggerInterface')]);
        try {
            $badResponse = <<<EOF
HTTP/1.1 503 Service Unavailable
Content-Type: application/json
Date: Thu, 04 Sep 2014 16:53:13 GMT

{"error": "something went wrong"}
EOF
;
            $this->assertRequest($client, [], function($request) use ($badResponse) {
                return $this->createMockResponse($badResponse);
            });

            $client->getGuzzle()->get('https://api.example.com/resource', []);
            $this->assertTrue(false, 'An exception should have been thrown before reaching this point!!!');
        } catch(ClientException $e) {
            $this->assertTrue($e->hasResponse());
            $this->assertEquals($badResponse, preg_replace('/\r/', '', (string) $e->getResponse()));
        }
    }

}
