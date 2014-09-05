<?php

namespace Renegare\HTTP\Test;

use Renegare\HTTP\JSONClient;
use Renegare\HTTP\GuzzlerTestTrait;

class JSONClientTest extends \PHPUnit_Framework_TestCase {
    use GuzzlerTestTrait;

    public function provideTestSupportedMethodsData() {
        return [
            ['get', ['some' => 'data'], null, false, 'http://api.example.com/some/resource?' . http_build_query(['some' => 'data'])],
            ['post', ['some' => 'data'], json_encode(['some' => 'data']), true, 'http://api.example.com/some/resource'],
            ['post', null, '', false, 'http://api.example.com/some/resource']
        ];
    }

    /**
     * @dataProvider provideTestSupportedMethodsData
     */
    public function testRequestBody($expectedMethod, $expectedData, $expectedBody, $expectedJsonContentType, $expectedUrl) {
        $expectedResource = 'some/resource';
        $expectedHeaders = ['SOME' => 'header'];
        $mockResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $client = new JSONClient('http://api.example.com', $this->getMock('Psr\Log\LoggerInterface'));

        $this->mockHTTPResponse($client, $expectedMethod, $expectedUrl, function($request) use ($mockResponse, $expectedBody, $expectedJsonContentType) {

            if($expectedJsonContentType) {
                $this->assertEquals('application/json', $request->getHeader('Content-Type'));
            } else {
                $this->assertNotEquals('application/json', $request->getHeader('Content-Type'));
            }

            return $mockResponse;
        });

        $this->assertSame($mockResponse, $client->$expectedMethod($expectedResource, $expectedData, $expectedHeaders));
    }

    /**
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testClientExceptionsAreThrown() {
        $client = new JSONClient('http://api.example.com', $this->getMock('Psr\Log\LoggerInterface'));

        $this->mockHTTPResponse($client, 'GET', 'http://api.example.com/some/resource', function($request) {
            throw new \GuzzleHttp\Exception\ClientException('WTH', $request);
        });

        $client->get('some/resource');
    }
}
