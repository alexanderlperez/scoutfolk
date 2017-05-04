<?php
/**
 * ScoutFolk: a gentle guide to the nutritional requirements of a jog to AnalogFolk HQ
 *
 * - Check REAME.md for more info.
 */

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once('./includes/helpers.php');

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$verify_token = getenv('VERIFY_TOKEN'); 
$token = getenv('ACCESS_TOKEN'); 

/**************
*  Bot init  *
**************/

interface IMessageExtractionStrategy {
    public function extract();
}

class UserMessageExtractor implements IMessageExtractionStrategy {
    private $message;

    public function __construct($message) {
        $this->message = $message;
    }

    public function extract() {
        return $this->message->message->text;
    }
}

class PostbackExtractor implements IMessageExtractionStrategy {
    private $message;

    public function __construct($message) {
        $this->message = $message;
    }

    public function extract() {
        return $this->message->postback->payload;
    }
}

class LocationExtractor implements IMessageExtractionStrategy {
    private $message;

    public function __construct($message) {
        $this->message = $message;
    }

    public function extract() {
        return $this->message->message->attachments[0]->payload->coordinates;
    }
}

class MessageHandler {
    private $extractionStrategy;

    public function __construct(IMessageExtractionStrategy $extractionStrategy) {
        $this->extractionStrategy = $extractionStrategy;
    }

    public function handle() {
        return $this->extractionStrategy->extract();
    }
}

// Handle token verification

$isTokenValidation = !empty($_REQUEST['hub_mode']) 
    && $_REQUEST['hub_mode'] == 'subscribe' 
    && $_REQUEST['hub_verify_token'] == $verify_token;

if ($isTokenValidation) {
    // end the response with the challenge key re. FB Msg requirements
    die($_REQUEST['hub_challenge']);
}

// Handle actual messages

$data = json_decode(file_get_contents("php://input"), false); // return obj
$hasMessage = !empty($data->entry[0]->messaging);

if ($hasMessage) {
    foreach ($data->entry[0]->messaging as $message) {
        $isUserMessage = !empty($message->message) 
            && !empty($message->message->text);

        $isLocation = !empty($message->message) 
            && !empty($message->message->attachments)
            && $message->message->attachments[0]->type == "location";

        $isPostback = !empty($message->postback);

        // Handle various "command payloads": user text, button text
        // - Initial user text
        // - Location - Quick Reply: string
        // - Bar selection - List Template: string

        if ($isUserMessage) {
            $strategy = new UserMessageExtractor($message);
        } else if ($isPostback) {
            $strategy = new PostbackExtractor($message);
        } else if ($isLocation) {
            $strategy = new LocationExtractor($message);
        }

        if (!isset($strategy)) {
            die('Unkown message type');
        }

        $handler = new MessageHandler($strategy);
        var_error_log($handler->handle());
    }
}

