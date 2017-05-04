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

use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\QuickReply;
use pimax\Messages\QuickReplyButton;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$verify_token = getenv('VERIFY_TOKEN'); 
$token = getenv('ACCESS_TOKEN'); 

/**************
*  Bot init  *
**************/

$bot = new FbBotApp($token);

// Handle token verification

$isTokenValidation = !empty($_REQUEST['hub_mode']) 
    && $_REQUEST['hub_mode'] == 'subscribe' 
    && $_REQUEST['hub_verify_token'] == $verify_token;

if ($isTokenValidation) {
    // end the response with the challenge key re. FB Msg requirements
    die($_REQUEST['hub_challenge']);
}

/***************************
*  Decision Tree-related  *
***************************/

function makeDecisionTreeDialogResponse($bot, $text, $userId) {
    if ($text == 'hi') {
        startDialog($bot, $userId);
    } else {
        errorDialog($bot, $text, $userId);
    }
}

function startDialog($bot, $userId) {
    $bot->send(new Message($userId, "Hi! We're going to figure out how far you need to jog to get to the AnalogFolk HQ, and how many Clif Bars you'll need to eat.  Let's start shall we..."));
    $bot->send(new QuickReply($userId, 'For starters, where are you?', [
        new QuickReplyButton(QuickReplyButton::TYPE_LOCATION),
    ]));
}

function errorDialog($bot, $text, $userId) {
    $bot->send(new Message($userId, "Hmmm, I'm not sure I get \"" . $text . "\""));
}

function makeMapRenderingResponse() {
    
}

/*************************
*  Handle the messages  *
*************************/

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

        if ($isUserMessage) {
            $strategy = new UserMessageExtractor($message);
            makeDecisionTreeDialogResponse($bot, $strategy->extract(), $message->sender->id);
        } else if ($isLocation) {
            $strategy = new LocationExtractor($message);
            makeMapRenderingResponse($strategy->extract(), $message->sender->id);
        } else if ($isPostback) {
            $strategy = new PostbackExtractor($message);
            //TODO: is this even used?
        } 

        if (!isset($strategy)) {
            error_log('Unkown message type');
            die('Unkown message type');
        }

        // TODO: handle the user appropriately
        // - simple dialog tree: 
        //   . inital user text -> start mapping
        //   . any user text while mapping -> respond with 
        //     * after bar choice -> "I don't know, but how about that favorite bar, huh?"
        //     * after location -> "I don't know, but I could figure out how far you need to jog"
        //     * after final rendering -> "Whew, I don't know, I'm a bit tired from all that calculating..."

        var_error_log($strategy->extract());
    }
}

