<?php

namespace Renegare\GuzzleClientHelper\Test;

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
        $assertRequest = $this->getMock('GuzzleHttp\Message\ResponseInterfce');
        $client = $this->getMockForAbstractClass('Renegare\GuzzleClientHelper\AbstractClient', ['http://api.example.com']);
        $client->expects($this->once())->method('request')->will($this->returnCallback(function($method = 'get', $resource=null, $data = null, array $headers = [])
            use ($assertRequest, $expectedMethod, $expectedResource, $expectedData, $expectedHeaders){

            $this->assertEquals($expectedMethod, $method);
            $this->assertEquals($expectedResource, $resource);
            $this->assertEquals($expectedData, $data);
            $this->assertEquals($expectedHeaders, $headers);

            return $assertRequest;
        }));

        $this->assertSame($assertRequest, $client->$expectedMethod($expectedResource, $expectedData, $expectedHeaders));
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testUnsupportedMethodException() {
        $client = $this->getMockForAbstractClass('Renegare\GuzzleClientHelper\AbstractClient', ['http://api.example.com']);
        $client->push();
    }
}
