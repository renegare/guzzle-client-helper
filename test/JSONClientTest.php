<?php

namespace Renegare\HTTP\Test;

use Renegare\HTTP\JSONClient;
use Renegare\HTTP\GuzzlerTestTrait;

class JSONClientTest extends \PHPUnit_Framework_TestCase {
    use GuzzlerTestTrait;

    public function provideTestSupportedMethodsData() {
        return [
            ['get', ['some' => 'data'], http_build_query(['some' => 'data']), false],
            ['post', ['some' => 'data'], json_encode(['some' => 'data']), true],
            ['post', null, '', false]
        ];
    }

    /**
     * @dataProvider provideTestSupportedMethodsData
     */
    public function testRequestBody($expectedMethod, $expectedData, $expectedBody, $expectedJsonContentType) {
        $expectedResource = 'some/resource';
        $expectedHeaders = ['SOME' => 'header'];
        $mockResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $client = new JSONClient('http://api.example.com', $this->getMock('Psr\Log\LoggerInterface'));

        $this->mockHTTPResponse($client, $expectedMethod, 'http://api.example.com/some/resource', function($request) use ($mockResponse, $expectedBody, $expectedJsonContentType) {

            $this->assertEquals($expectedBody, (string) $request->getBody());

            if($expectedJsonContentType) {
                $this->assertEquals('application/json', $request->getHeader('Content-Type'));
            } else {
                $this->assertEquals('', $request->getHeader('Content-Type'));
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
