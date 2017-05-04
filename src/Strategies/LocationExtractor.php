<?php

namespace ScoutFolk\Strategies;

use ScoutFolk\Interfaces\IMessageExtractionStrategy;

class LocationExtractor implements IMessageExtractionStrategy {
    private $message;

    public function __construct($message) {
        $this->message = $message;
    }

    public function extract() {
        return $this->message->message->attachments[0]->payload->coordinates;
    }
}

