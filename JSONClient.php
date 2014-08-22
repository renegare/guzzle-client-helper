<?php

namespace Renegare\HTTP;

class JSONClient extends AbstractClient {

    /**
     * {@inheritdoc}
     */
    protected function request($method = 'get', $resource=null, $data = null, array $headers = []) {
        $options = ['headers' => $headers];

        $type = $method !== 'get' && is_array($data) ? 'json' : 'body';
        $options[$type] = $data;

        $this->debug('Requesting platform resource ...');
        $request = $this->createRequest($method, $resource, $options);
        return $this->getGuzzle()->send($request);
    }
}
