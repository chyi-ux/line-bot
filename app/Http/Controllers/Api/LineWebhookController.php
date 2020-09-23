<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Exception;

class LineWebhookController extends Controller
{

    public function webhook (Request $request)
    {
        $lineAccessToken = "hjKHd9NmW6o6TtGiIfeQLqP9kSD82A/CSUmCZI2OYSmorMBoWPJIbm/xwwmAwbKzCRU3vQfA+FkNjwof146/Ckaie0u5f9bxVYFGE5KKtUCRjUZPcXFk+tC6m1JtzT7inNzj+9SPylSf8SzW7eSVOwdB04t89/1O/w1cDnyilFU="; //前面申請到的Channel acess token(long-lived)
        $lineChannelSecret = "a5eb7f7e5d7dc1ce9d7432cf61381af8";//前面申請到的Channel secret

       
        $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
        if (!SignatureValidator::validateSignature($request->getContent(), $lineChannelSecret, $signature)) {
           
            return;
        }

        $httpClient = new CurlHTTPClient ($lineAccessToken);
        $lineBot = new LINEBot($httpClient, ['channelSecret' => $lineChannelSecret]);

        try {
          
            $events = $lineBot->parseEventRequest($request->getContent(), $signature);

            foreach ($events as $event) {
                
                $replyToken = $event->getReplyToken();
                  $text = $event->getText();// 得到使用者輸入
           $lineBot->replyText($replyToken, $text);// 回復使用者輸入
                //$textMessage = new TextMessageBuilder("你好");
              //  $lineBot->replyMessage($replyToken, $textMessage);
            }
        } catch (Exception $e) {
           
            return;
        }

        return;
    }
}