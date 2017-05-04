<?php
/**
 * ScoutFolk: a gentle guide to the nutritional requirements of a jog to AnalogFolk HQ
 *
 * - Check REAME.md for more info.
 */

require_once(dirname(__FILE__) . '/vendor/autoload.php');

use Symfony\Component\Dotenv\Dotenv;
use ScoutFolk\Strategies\UserMessageExtractor;
use ScoutFolk\Strategies\PostbackExtractor;
use ScoutFolk\Strategies\LocationExtractor;
use ScoutFolk\Controllers\MessageHandler;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$verify_token = getenv('VERIFY_TOKEN'); 
$token = getenv('ACCESS_TOKEN'); 

/**************
*  Bot init  *
**************/

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
            error_log('Unkown message type');
            die('Unkown message type');
        }

        var_error_log($strategy->extract());
    }
}

