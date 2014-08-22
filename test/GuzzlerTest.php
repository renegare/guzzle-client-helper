<?php

namespace Renegare\HTTP\Test;

class GuzzlerTest extends \PHPUnit_Framework_TestCase {

    public function testGuzzler() {
        $client = $this->getMockForAbstractClass('Renegare\HTTP\AbstractClient', ['http://api.example.com', $this->getMock('Psr\Log\LoggerInterface')]);
        $this->assertInstanceOf('GuzzleHttp\Client', $client->getGuzzle());
        //  $mockGuzzle = $this->getMockBuilder('GuzzleHttp\Client')
            // ->set
    }
}
