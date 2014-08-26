<?php

namespace Renegare\HTTP;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Subscriber\Log\LogSubscriber;

trait GuzzlerTrait {

    protected $guzzleHttp;

    /**
     * {@inheritdoc}
     */
    public function setGuzzle(GuzzleClient $client = null) {
        $this->guzzleHttp = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getGuzzle() {
        if(!$this->guzzleHttp) {
            $client = new GuzzleClient();
            if($this->logger) {
                $subscriber = new LogSubscriber($this->logger);
                $client->getEmitter()->attach($subscriber);
            }
            $this->guzzleHttp = $client;
        }
        return $this->guzzleHttp;
    }
}
