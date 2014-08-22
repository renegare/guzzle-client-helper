<?php

namespace Renegare\HTTP\Test;

use Renegare\HTTP\JSONClient;
use Renegare\HTTP\GuzzlerTestTrait;

class JSONClientTest extends \PHPUnit_Framework_TestCase {
    use GuzzlerTestTrait;

    public function provideTestSupportedMethodsData() {
        return [
            ['get', false],
            ['post', true]
        ];
    }

    /**
     * @dataProvider provideTestSupportedMethodsData
     */
    public function testRequestBody($expectedMethod, $expectedJsonContentType) {
        $expectedResource = 'some/resource';
        $expectedData = ['some' => 'data'];
        $expectedHeaders = ['SOME' => 'header'];
        $mockResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $client = new JSONClient('http://api.example.com', $this->getMock('Psr\Log\LoggerInterface'));

        $this->mockHTTPResponse($client, $expectedMethod, 'http://api.example.com/some/resource', function($request) use ($mockResponse, $expectedData, $expectedJsonContentType) {

            $rawBody = (string) $request->getBody();

            if($expectedJsonContentType) {
                $this->assertEquals('application/json', $request->getHeader('Content-Type'));
                $this->assertEquals($expectedData, json_decode($rawBody, true));
            } else {
                $this->assertNotEquals('application/json', $request->getHeader('Content-Type'));
                parse_str($rawBody, $query);
                $this->assertEquals($expectedData, $query);
            }

            return $mockResponse;
        });

        $this->assertSame($mockResponse, $client->$expectedMethod($expectedResource, $expectedData, $expectedHeaders));
    }
}
