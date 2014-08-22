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
        $this->info('set guzzle mock', ['guzzle' => $this->guzzleHttp]);
    }

    /**
     * {@inheritdoc}
     */
    public function getGuzzle() {
        $this->info('get guzzle mock', ['guzzle' => $this->guzzleHttp]);
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
