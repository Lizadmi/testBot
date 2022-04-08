<?php

namespace App\Http\Controllers;

use App\Models\UserList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use function Symfony\Component\HttpFoundation\add;
use function Symfony\Component\Mime\toString;

class TelegramController extends Controller
{
    public function getUpdates()
    {
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

        $weather = json_decode(\Illuminate\Support\Facades\Http::get("http://api.weatherapi.com/v1/current.json", [
            "key" => "5dec626e40c44d9193471014220704",
            "q" => "Moscow"
        ]), JSON_OBJECT_AS_ARRAY);

        $temperature = $weather["current"]["temp_c"];

        $response = (json_decode(\Illuminate\Support\Facades\Http::get("https://api.telegram.org/bot" . config('bot.bot') . "/getUpdates"), JSON_OBJECT_AS_ARRAY));

        foreach ($response["result"] as $resp) {

            if ($resp["message"]["text"] == "/start" and !UserList::where("id_user", $resp["message"]["from"]["id"])->count()) {
                $userList = new UserList();
                $userList->id_user = $resp["message"]["from"]["id"];
                $userList->is_bot = $resp["message"]["from"]["is_bot"];
                $userList->first_name = $resp["message"]["from"]["first_name"];
                $userList->last_name = $resp["message"]["from"]["last_name"];
                $userList->username = $resp["message"]["from"]["username"];
                $userList->language_code = $resp["message"]["from"]["language_code"];
                $userList->save();

                return \Illuminate\Support\Facades\Http::attach('photo', Storage::get('/public/pngTest.png'), 'pngTest.png')->post("https://api.telegram.org/bot" . config('bot.bot') . "/sendPhoto", [
                    'chat_id' => $resp["message"]["chat"]["id"],
                    'caption' => "Hello",
                    'reply_markup' => json_encode($keyboard)
                ]);
            }
            if ($resp["message"]["text"] == "Текущая температура воздуха МСК") {
                return \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot" . config('bot.bot') . "/sendMessage", [
                    'chat_id' => $resp["message"]["chat"]["id"],
                    'text' => "$temperature",
                ]);
            }


            if ($resp["message"]["text"] == "Список пользователей") {

                $userList = UserList::all("first_name", "last_name", "created_at");

                $inlineKeyboard = [
                    "inline_keyboard" => []
                ];

                foreach ($userList as $user) {
                    array_push($inlineKeyboard["inline_keyboard"], [[
                        "text" => $user->first_name . " " . $user->last_name,
                        "callback_data" => $user->created_at->format('d-m-Y')
                    ]
                    ]);
                }

                return \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot" . config('bot.bot') . "/sendMessage", [
                    'chat_id' => $resp["message"]["chat"]["id"],
                    'text' => "Список пользователей",
                    'reply_markup' => json_encode($inlineKeyboard)
                ]);
            }
        }
    }
}
