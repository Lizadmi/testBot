<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TelegramController extends Controller
{
    public function getUpdates()
    {
        $lastMessage = "";

        $params = [
            "offset"=>$lastMessage,
        ];

        $keyboard = [
            "keyboard" => [
                [
                    ["text" => "Ввести имя"],
                    ["text" => "Текущая температура воздуха МСК"],
                    ["text" => "Список пользователей"],
                ]
            ],
            "one_time_keyboard" => true,
            "resize_keyboard" => true
        ];

        $weather =  json_decode(\Illuminate\Support\Facades\Http::get("http://api.weatherapi.com/v1/current.json",[
            "key"=>"5dec626e40c44d9193471014220704",
            "q"=>"Moscow"
        ]),JSON_OBJECT_AS_ARRAY);

        $temperature = $weather["current"]["temp_c"];



        $response = (json_decode(\Illuminate\Support\Facades\Http::get("https://api.telegram.org/bot" . config('bot.bot') . "/getUpdates"), JSON_OBJECT_AS_ARRAY));

        dd($response["result"]);

        foreach ($response["result"] as $resp) {
            var_dump($resp);
//            echo $resp["message"]["from"]["first_name"]."<br>";
//            echo $resp["message"]["from"]["last_name"]."<br>";
//            echo $resp["message"]["from"]["username"]."<br><br>";

//            if ($resp["message"]["text"] == "/start") {
//                return \Illuminate\Support\Facades\Http::attach('photo', Storage::get('/public/pngTest.png'), 'pngTest.png')->post("https://api.telegram.org/bot" . config('bot.bot') . "/sendPhoto", [
//                    'chat_id' => $resp["message"]["chat"]["id"],
//                    'caption' => "Hello",
//                    'reply_markup' => json_encode($keyboard)
//                ]);
//            }
//            if ($resp["message"]["text"] == "Текущая температура воздуха МСК") {
//                return \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot" . config('bot.bot') . "/sendMessage", [
//                    'chat_id' => $resp["message"]["chat"]["id"],
//                    'text' => "$temperature",
//                ]);
//            }
        }
    }
}
