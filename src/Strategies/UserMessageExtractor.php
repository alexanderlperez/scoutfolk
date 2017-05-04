<?php

namespace ScoutFolk\Strategies;

use ScoutFolk\Interfaces\IMessageExtractionStrategy;

class UserMessageExtractor implements IMessageExtractionStrategy {
    private $message;

    public function __construct($message) {
        $this->message = $message;
    }

    public function extract() {
        return $this->message->message->text;
    }
}
