<?php

namespace Renegare\GuzzleClientHelper;

use Psr\Log\LoggerTrait as PsrLoggerTrait;
use Psr\Log\LoggerAwareTrait;

trait LoggerTrait {
    use PsrLoggerTrait, LoggerAwareTrait;

    protected $messagePreffix = '';

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array()) {
        if($this->logger) {
            $this->logger->log($level, $this->messagePreffix . $message, $context);
        }
    }
}
