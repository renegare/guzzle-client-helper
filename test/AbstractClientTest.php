<?php

namespace Renegare\HTTP\Test;

class AbstractClientTest extends \PHPUnit_Framework_TestCase {

    public function provideTestSupportedMethodsData() {
        return [
            ['get'],
            ['post'],
            ['put'],
            ['delete'],
            ['patch'],
            ['options'],
            ['head']
        ];
    }

    /**
     * @dataProvider provideTestSupportedMethodsData
     */
    public function testSupportedMethods($expectedMethod) {
        $expectedResource = 'some/resource';
        $expectedData = ['some' => 'data'];
        $expectedHeaders = ['SOME' => 'header'];
        $mockResponse = $this->getMock('GuzzleHttp\Message\ResponseInterfce');
        $client = $this->getMockForAbstractClass('Renegare\HTTP\AbstractClient', ['http://api.example.com']);
        $client->expects($this->once())->method('request')->will($this->returnCallback(function($method = 'get', $resource=null, $data = null, array $headers = [])
            use ($mockResponse, $expectedMethod, $expectedResource, $expectedData, $expectedHeaders){

            $this->assertEquals($expectedMethod, $method);
            $this->assertEquals($expectedResource, $resource);
            $this->assertEquals($expectedData, $data);
            $this->assertEquals($expectedHeaders, $headers);

            return $mockResponse;
        }));

        $this->assertSame($mockResponse, $client->$expectedMethod($expectedResource, $expectedData, $expectedHeaders));
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testUnsupportedMethodException() {
        $client = $this->getMockForAbstractClass('Renegare\HTTP\AbstractClient', ['http://api.example.com']);
        $client->push();
    }
}
