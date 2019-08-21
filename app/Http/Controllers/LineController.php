<?php

namespace App\Http\Controllers;

use App\Line\Flex\FlexVegetable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\LocationTemplateActionBuilder;

class LineController extends Controller
{
    /**
     * Property client.
     *
     * @var  \GuzzleHttp\Client
     */
    private $client;

    private $bot;

    private $channel_access_token;

    private $channel_secret;

    public function __construct(Client $client)
    {
        $this->channel_access_token = env('CHANNEL_ACCESS_TOKEN');
        $this->channel_secret = env('CHANNEL_SECRET');

        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($this->channel_access_token);
        $this->bot  = new \LINE\LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);

        $this->client = $client;
    }

    public function webhook(Request $request)
    {
        // Verify LINE Chatbot  signature
        $bot = $this->bot;
        $signature = $request->header(\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);
        $body      = $request->getContent();
        try {
            $events = $bot->parseEventRequest($body, $signature);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        foreach ($events as $event) {
            $line_user_id = $event->getUserId();
            $replyToken = $event->getReplyToken();

            if ($event instanceof FollowEvent) {
                $bot->replyText($replyToken, '嗨，我是菜哥，歡迎和我詢問任何蔬菜的價錢，試著直接輸入『蘋果』');
            } elseif ($event instanceof UnfollowEvent) {
                Log::info('UnfollowEvent');
            } elseif ($event instanceof MessageEvent) {
                $message_type = $event->getMessageType();
                $text = $event->getText();

                switch ($message_type) {
                    case 'text':
                        $query_result = self::getVegetableData($text);
                        
                        $flex_message = FlexVegetable::get($query_result);
                        $bot->replyMessage($replyToken, $flex_message);
                        break;
                }
            }
        }
    }

    private static function getVegetableData($query_text)
    {
        $farmTransData = 'http://data.coa.gov.tw/Service/OpenData/FromM/FarmTransData.aspx';

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $farmTransData);
        $vegetables = json_decode((string)$response->getBody(), true);

        $query_result = array();
        foreach ($vegetables as $vegetable){
            if (preg_match('/^'.$query_text.'/', $vegetable['作物名稱'])){
                array_push($query_result, $vegetable);
            }
        }

        return $query_result;
    }
}