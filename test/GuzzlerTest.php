<?php

namespace Renegare\HTTP\Test;

class GuzzlerTest extends \PHPUnit_Framework_TestCase {
    use \Renegare\HTTP\GuzzlerTestTrait;

    public function testGuzzler() {
        $client = $this->getMockForAbstractClass('Renegare\HTTP\AbstractClient', ['http://api.example.com', $this->getMock('Psr\Log\LoggerInterface')]);
        $this->assertInstanceOf('GuzzleHttp\Client', $client->getGuzzle());
    }

    public function testGuzzlerTestTraitAssertCalled() {
        $client = $this->getMockForAbstractClass('Renegare\HTTP\AbstractClient', ['http://api.example.com', $this->getMock('Psr\Log\LoggerInterface')]);
        $this->mockHTTPResponse($client, 'POST', 'http://api.example.com/resource', function(){
            return ['response' => '...'];
        });

        $response = $client->getGuzzle()->post('http://api.example.com/resource');
        $this->assertInstanceOf('GuzzleHttp\Message\ResponseInterface', $response);
        $this->assertEquals(['response' => '...'], $response->json());
    }
}
