<?php

namespace Renegare\HTTP;

use Psr\Log\LoggerInterface;

abstract class AbstractClient implements LoggerInterface, GuzzlerInterface {
    use LoggerTrait, GuzzlerTrait;

    /**
     * @param string $endPoint - http://api.endpoint.com (no trailing slash)
     * @param LoggerInterface $logger [optional]
     */
    public function __construct($baseUrl, LoggerInterface $logger = null) {
        $this->logger = $logger;
        $this->baseUrl = $baseUrl;
    }

    /**
     * magic function to handle http method requests
     * {@inheritdoc}
     * @throws RuntimeException for unsupported http methods
     * @return GuzzleHttp\Message\ResponseInterface
     */
    public function __call($method, $args) {
        $supportedHTTPMethods = $this->getSupportedHTTPMethods();
        if(!in_array($method, $supportedHTTPMethods)) {
            throw new \BadMethodCallException(sprintf('Unsupported HTTP method %s. This client supports only the following methods %s', $method, implode(', ', $supportedHTTPMethods)));
        }
        array_unshift($args, strtolower($method));
        return call_user_func_array([$this, 'request'], $args);
    }

    public function getSupportedHTTPMethods() {
        return ['get', 'post', 'put', 'delete', 'patch', 'options', 'head'];
    }

    abstract protected function request($method = 'get', $resource=null, $data = null, array $headers = []);

    protected function createRequest($method, $resource=null, array $options = []) {
        return $this->getGuzzle()->createRequest($method, $resource, $options);
    }
}
