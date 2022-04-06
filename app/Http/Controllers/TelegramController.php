<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function getUpdates()
    {
        $lastupdate = '41304312';

        $params = [
            'offset' => $lastupdate,
        ];

        $response = (json_decode(\Illuminate\Support\Facades\Http::get("https://api.telegram.org/bot".config('bot.bot')."/getUpdates"), JSON_OBJECT_AS_ARRAY));

        foreach ($response["result"] as $resp){
            var_dump($resp);
            echo $resp["message"]["chat"]["id"]."   ";
            echo $resp["message"]["text"]."<br>";
            echo $resp["message"]["text"]."<br>";

        }
    }
}
