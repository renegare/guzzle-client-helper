<?php

namespace Renegare\HTTP;

use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Message\ResponseInterface;

trait GuzzlerTestTrait {
    public function mockHTTPResponse(GuzzlerInterface $client, $expectedMethod = 'GET', $expectedResource, \Closure $requestCallback = null, $expectToBeCalled = true) {
        $mockHttpClient = $this->getMockBuilder('GuzzleHttp\Client')
            ->setMethods(['send'])
            ->getMock();

        $mockHttpClient->expects($expectToBeCalled? $this->any() : $this->never())
            ->method('send')
            ->will($this->returnCallback(function($request) use ($client, $expectedResource, $expectedMethod, $requestCallback){
                $client->setGuzzle(null);
                $this->assertEquals($expectedResource, $request->getUrl());
                $this->assertEquals(strtolower($expectedMethod), strtolower($request->getMethod()));

                $response = $requestCallback? $requestCallback($request) : [];

                if(!($response instanceof ResponseInterface)) {
                    $factory = new MessageFactory();
                    $fakeJson = json_encode($response? $response : '');
                    $responseMessage = sprintf('HTTP/1.1 %s OK
Content-Type: application/json
Date: %s
Content-Length: %s
Connection: keep-alive

%s
', 200, date('r'), strlen($fakeJson), json_encode($response));

                    $response = $factory->fromMessage($responseMessage);
                }

                return $response;
            }));

        $client->setGuzzle($mockHttpClient);
    }
}
