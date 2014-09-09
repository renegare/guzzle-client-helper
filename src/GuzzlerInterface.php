<?php

namespace Renegare\GuzzleClientHelper;

use GuzzleHttp\Client as GuzzleClient;

interface GuzzlerInterface {
    /**
     * @return Client $client [no argument will unset the client]
     */
    public function setGuzzle(GuzzleClient $client = null);

    /**
     * @return Client|null
     */
    public function getGuzzle();
}
