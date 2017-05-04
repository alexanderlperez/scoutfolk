<?php

namespace ScoutFolk\Controllers;

class MessageHandler {
    private $extractionStrategy;

    public function __construct(IMessageExtractionStrategy $extractionStrategy) {
        $this->extractionStrategy = $extractionStrategy;
    }

    public function handle() {
        return $this->extractionStrategy->extract();
    }
}


