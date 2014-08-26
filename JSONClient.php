<?php

namespace Renegare\HTTP;

use GuzzleHttp\Exception\ClientException;

class JSONClient extends AbstractClient {

    /**
     * {@inheritdoc}
     */
    protected function request($method = 'get', $resource=null, $data = null, array $headers = []) {
        $options = ['headers' => $headers];
        $type = $method !== 'get' && is_array($data) ? 'json' : 'body';
        if($data) {
            $options[$type] = $data;
        }

        $request = $this->createRequest($method, $resource, $options);
        $this->debug('<< Requesting platform resource ...', ['request' => (string) $request]);
        try {
            $response = $this->getGuzzle()->send($request);
            $this->debug('>> Response from platform resource ...', ['response' => (string) $response]);
        } catch (ClientException $e) {
            $this->error('!! Platform error response', ['response' => (string) $e->getResponse()]);
            throw $e;
        }
        return $response;
    }
}
