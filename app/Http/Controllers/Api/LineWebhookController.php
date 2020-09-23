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
            $image = "original_content_url='https://www.sample-videos.com/img/Sample-jpg-image-50kb.jpg',preview_image_url='https://www.sample-videos.com/img/Sample-jpg-image-50kb.jpg'";
            foreach ($events as $event) {
                
                $replyToken = $event->getReplyToken();
                $text = $event->getText();// 得到使用者輸入
                if ($text == "歲末驚喜") {
                	//輪播型(僅手機看的到)
					$columns = array();
					$img_url = "https://www.sample-videos.com/img/Sample-jpg-image-50kb.jpg";
					for($i=0;$i<5;$i++) //最多5筆
					{
					  $actions = array(
					    //一般訊息型 action
					    new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("按鈕1","文字1"),
					    //網址型 action
					    new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("觀看食記","http://www.google.com")
					  );
					  $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder("標題".$i, "說明".$i, $img_url , $actions);
					  $columns[] = $column;
					}
					$carousel = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columns);
					$msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $carousel);
                	$lineBot->replyMessage($replyToken,$msg);// 回復使用者輸入
                }
                if ($text == "粉絲獨享") {
                	$actions = array(
					  new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("是", "ans=Y"),
					  new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("否", "ans=N")
					);
					$button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder("問題", $actions);
					$msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $button);
                	$lineBot->replyMessage($replyToken,$msg);// 回復使用者輸入
                }
                if ($text == "常見問題") {
                	$actions = array(
					  //一般訊息型 action
					  new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("按鈕1","文字1"),
					  //網址型 action
					  new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("Google","http://www.google.com"),
					  //下列兩筆均為互動型action
					  new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("下一頁", "page=3"),
					  new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("上一頁", "page=1")
					);

					$img_url = "https://www.sample-videos.com/img/Sample-jpg-image-50kb.jpg";
					$button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("按鈕文字","說明", $img_url, $actions);
					$msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $button);
					$lineBot->replyMessage($replyToken,$msg);// 回復使用者輸入
                }
                if ($text == "熱銷必敗") {
                	$msg = new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder("群義房屋", "台中市南屯區文心路一段424號", 24.1503955, 120.646975);
                	$lineBot->replyMessage($replyToken, $msg);// 回復使用者輸入
                }
                if ($text == "推薦好友") {
                	$packageId = '1';
                	$stickerId = '2';
                	$msg = new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder($packageId,$stickerId);
                	$lineBot->replyMessage($replyToken, $msg);// 回復使用者輸入
                }
                else
                {
                	$lineBot->replyText($replyToken, "您在說什麼我聽不懂~");// 回復使用者輸入
                }
           		
                //$textMessage = new TextMessageBuilder("你好");
              //  $lineBot->replyMessage($replyToken, $textMessage);
            }
        } catch (Exception $e) {
           
            return;
        }

        return;
    }
}