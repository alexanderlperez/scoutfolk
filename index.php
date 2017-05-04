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
use pimax\Messages\ImageMessage;
use pimax\Messages\Message;
use pimax\Messages\QuickReply;
use pimax\Messages\QuickReplyButton;
use pimax\Messages\SenderAction;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$verify_token = getenv('VERIFY_TOKEN'); 
$token = getenv('ACCESS_TOKEN'); 
$map_key = getenv('MAP_KEY');
$static_map_key = getenv('STATIC_MAP_KEY');

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

// TODO: refactor to controller...

function makeDecisionTreeDialogResponse($bot, $text, $userId) {
    // TODO: handle the user appropriately
    // - simple dialog tree: 
    //   . inital user text -> start mapping
    //   . any user text while mapping -> respond with 
    //     * after bar choice -> "I don't know, but how about that favorite bar, huh?"
    //     * after location -> "I don't know, but I could figure out how far you need to jog"
    //     * after final rendering -> "Whew, I don't know, I'm a bit tired from all that calculating..."

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

function makeMapRenderingResponse($bot, $userId) {
    $bot->send(new Message($userId, "This is great! I'm going map out your route to AnalogFolks HQ..."));
    $bot->send(new SenderAction($userId, SenderAction::ACTION_TYPING_ON));

    // create the map image

    // TODO: actually create map with gotten location coordinates
    $mapImage = "https://maps.googleapis.com/maps/api/staticmap?center=Minsk&zoom=6&size=600x400&maptype=roadmap&path=enc%3AohqfIc_jpCkE%7DCx@mJdDa[bD%7BM%7D@e@_MgKiQuVOoFlF%7DVnCnBn@aDxCwNoD%7DHl@%7DNkByE%5Eqt@vFm|@dMkcBrQmv@lK%7Bu@~N_cBdWshEp_@%7DjGpHw_@rg@k_B|dB%7DmFvm@ulBdb@ulBxq@muCpoAggHxy@ygFbA_]aAawA%5CecAzGkx@nUuxA|D%7Bt@aBoeAwKwaAqG%7B%5CeBc_@p@aZx%60@gcGpNg|BGmWa%5CgpFyZolF%7BFgcDyPy|EoK_%7BAwm@%7BqFqZaiBoNsqCuNq%7BHk%60@crG%7B]qkBul@guC%7BJ%7D]aNo%7B@k%5EqfBkb@kfCsLc_@m|Ae~Cee@aaAcMqWsc@kjAwZsj@a%5E%7Dn@sSwk@u~@qhEkrBeiJsVkgAcGoNq_BetDkk@oqAqRcl@w%5CmmA%7BJs%5CoM%7DSga@on@qf@yu@wwAyxBkoAooBi%7B@utA%7Dp@eoAyl@ogAan@ei@uHyOiS_v@%7D%7DAeyFc%5CelAoJ_i@gBad@dA_a@f]i%60Evp@e_IrYmcDxJiyAjD%7BdBrCcnH|AqyEoIefBwJuw@eKoi@%7Bf@eeBoRagA%7BEsw@q@%7DgAnNcnCtIwoBdBitA_Cyo@me@%7DhEyMws@ie@qyAaaAspBqz@ceBaPq%5EeMcc@%7BI%7Bg@sGg%7B@%5CyqBlF%7BrEjAyzCuHa|@sKaf@oNg%60@y]_j@kc@wq@oSqr@oJys@kf@uxIOk%7B@tFuhAbEwl@pOklAvk@%7DgEjBo%5EwEgt@_l@scCoLio@iDyb@q@_z@%60N%7BkDNwr@wBsb@%7BHmn@yNibAsJyq@eOu_C%7DJocAqN_n@%7D[yu@c%5Eah@sgBqkCoOk%60@%7DMmm@mq@qzEumBmwK%7DUw~BmKktBuJobBsNwdAgZgzA_Nsf@%7B%7B@wwCcTqZqn@uq@kJ%7DOmG%7BT%7BMsx@cOaxCwH_g@ufCisFikBedEkLoh@q@eWdB%7B]bSeu@vxByyGbKqp@%60HchClG%7D%7BEcKejDgRkeHaGylAkHex@oWcjBaGmaAMevBtF%7DfAtMceA%60Se_AhUmaAb|AkyGjf@_uBvx@gaDl%7BBihIlY%7DjApGmk@%60XkiDbNaiBnA%7Bp@xAs~AfJk%7D@fH%7De@pJy%5EdZoj@|T%7B%60@~Rgu@tWahAdGkOxW%7BV%60_@qXjLgNbKoQzf@%7DhAfZeq@jWw%7B@lVodAnOgy@jh@mrItGc%60AtF_b@lBss@yZwgDsKyvC|CqkAvFqt@dg@qhDxLkv@vB_]_Aw%60@uMkrBcHuwAbFok@rM__AzEioAlCmoB_Dat@wK_dDoEigB|CcQbEqM~Bk%5ClEg_ApEg_AxAg%5CxJyZzNqc@gCyMuEoMsJ%7BYiBgF";

    $bot->send(new ImageMessage($userId, $mapImage));
    $bot->send(new Message($userId, "... Feeling hungry yet?"));
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
            makeMapRenderingResponse($bot, $message->sender->id);
        } else if ($isPostback) {
            $strategy = new PostbackExtractor($message);
            //TODO: is this even used?
        } 

        if (!isset($strategy)) {
            error_log('Unkown message type');
            die('Unkown message type');
        }

        var_error_log($strategy->extract());
    }
}

