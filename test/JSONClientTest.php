<?php

namespace Renegare\HTTP\Test;

use Renegare\HTTP\JSONClient;
use Renegare\HTTP\GuzzlerTestTrait;

class JSONClientTest extends \PHPUnit_Framework_TestCase {
    use GuzzlerTestTrait;

    public function provideTestSupportedMethodsData() {
        return [
            ['get', ['some' => 'data'], ''],
            ['post', [], json_encode(['some' => 'data'])]
        ];
    }

    /**
     * @dataProvider provideTestSupportedMethodsData
     */
    public function testRequestBody($expectedMethod, $expectedQuery, $expectedBody) {
        $expectedResource = 'some/resource';

        $client = new JSONClient('http://api.example.com', $this->getMock('Psr\Log\LoggerInterface'));

        $this->assertRequest($client, [
            'method' => $expectedMethod,
            'query' => $expectedQuery,
            'body' => $expectedBody]);

        $client->$expectedMethod($expectedResource, ['some' => 'data']);
    }

    /**
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testClientExceptionsAreThrown() {
        $client = new JSONClient('http://api.example.com', $this->getMock('Psr\Log\LoggerInterface'));

        $this->assertRequest($client, [
            'method' => 'GET',
            'path' => '/some/resource'
        ], function($request) {
            throw new \GuzzleHttp\Exception\ClientException('WTH', $request, $this->getMock('GuzzleHttp\Message\ResponseInterface'));
        });

        $client->get('some/resource');
    }
}
