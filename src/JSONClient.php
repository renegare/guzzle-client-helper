<?php

namespace Renegare\GuzzleClientHelper;

use GuzzleHttp\Exception\ClientException;

class JSONClient extends AbstractClient {

    /**
     * {@inheritdoc}
     */
    protected function request($method = 'get', $resource=null, $data = null, array $headers = []) {
        $options = ['headers' => $headers];

        if(is_array($data)) {
            if($method === 'get') {
                $options['query'] = $data;
            } else {
                $options['json'] = $data;
            }
        } else {
            $options['body'] = $data;
        }

        $request = $this->createRequest($method, $resource, $options);
        $this->debug('<< Requesting platform resource ...', ['request' => (string) $request]);
        try {
            $response = $this->getGuzzle()->send($request);
            $this->debug('>> Response from platform resource ...', ['response' => (string) $response]);
        } catch (ClientException $e) {
            if($e->hasResponse()) {
                $response = $e->getResponse();
                $this->error('!! Error response ' . $response->getStatusCode(), ['response' => (string) $response]);
            }
            throw $e;
        }
        return $response;
    }
}
