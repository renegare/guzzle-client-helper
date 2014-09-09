<?php

namespace Renegare\GuzzleClientHelper;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Subscriber\Log\LogSubscriber;

trait GuzzlerTrait {

    protected $guzzleHttp;
    protected $baseUrl;

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
            $client = new GuzzleClient(['base_url' => $this->baseUrl]);
            if($this->logger) {
                $subscriber = new LogSubscriber($this->logger);
                $client->getEmitter()->attach($subscriber);
            }
            $this->guzzleHttp = $client;
        }
        return $this->guzzleHttp;
    }
}
