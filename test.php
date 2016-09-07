<?php

$access_token = '214473630:AAHx0uBoSJ5_SqYdk8kIWAS0KmjunAt1x_A';
$api = 'https://api.telegram.org/bot' . $access_token;

$output = json_decode(file_get_contents('php://input'), TRUE);
$chat_id = $output['message']['chat']['id'];
$first_name = $output['message']['chat']['first_name'];
$message = $output['message']['text'];

switch($message) {
    case '/pogoda':
        $preload_text = 'Одну секунду, ' . $first_name . ' ' . ' Я уточняю для вас погоду..';
        sendMessage($chat_id, $preload_text);
        $apikey= $access_token;
        // ID для города/района/местности (есть все города РФ).
        $id = '500776';
        // Получаем JSON-ответ от OpenWeatherMap.
        $pogoda = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?appid=' . $apikey . '&id=' . $id . '&units=metric&lang=ru'), TRUE);
        // Определяем тип погоды из ответа и выводим соответствующий Emoji.
        if ($pogoda['weather'][0]['main'] === 'Clear') { $pogoda['weather'][0]['description']; }
        elseif ($pogoda['weather'][0]['main'] === 'Clouds') { $pogoda['weather'][0]['description']; }
        elseif ($pogoda['weather'][0]['main'] === 'Rain') { $pogoda['weather'][0]['description']; }
        elseif ($pogoda['weather'][0]['main'] === 'Snow') { $pogoda['weather'][0]['description']; }
        else $weather_type = $pogoda['weather'][0]['description'];
        // Температура воздуха.
        if ($pogoda['main']['temp'] > 0) { $temperature = '+' . sprintf("%d", $pogoda['main']['temp']); }
        else { $temperature = sprintf("%d", $pogoda['main']['temp']); }
        // Направление ветра.
        if ($pogoda['wind']['deg'] >= 0 && $pogoda['wind']['deg'] <= 11.25) { $wind_direction = 'северный'; }
        elseif ($pogoda['wind']['deg'] > 11.25 && $pogoda['wind']['deg'] <= 78.75) { $wind_direction = 'северо-восточный, '; }
        elseif ($pogoda['wind']['deg'] > 78.75 && $pogoda['wind']['deg'] <= 101.25) { $wind_direction = 'восточный, '; }
        elseif ($pogoda['wind']['deg'] > 101.25 && $pogoda['wind']['deg'] <= 168.75) { $wind_direction = 'юго-восточный, '; }
        elseif ($pogoda['wind']['deg'] > 168.75 && $pogoda['wind']['deg'] <= 191.25) { $wind_direction = 'южный, '; }
        elseif ($pogoda['wind']['deg'] > 191.25 && $pogoda['wind']['deg'] <= 258.75) { $wind_direction = 'юго-западный, '; }
        elseif ($pogoda['wind']['deg'] > 258.75 && $pogoda['wind']['deg'] <= 281.25) { $wind_direction = 'западный, '; }
        elseif ($pogoda['wind']['deg'] > 281.25 && $pogoda['wind']['deg'] <= 348.75) { $wind_direction = 'северо-западный, '; }
        else { $wind_direction = 'yhhyyhyththyhth '; }
        $weather_text = 'Сейчас ' . '. Температура воздуха: ' . $temperature . '°C. Ветер ' . $wind_direction . sprintf("%u", $pogoda['wind']['speed']) . ' м/сек.';
        // Отправка ответа пользователю Telegram.
        sendMessage($chat_id, $weather_text);
        break;
    default:
        break;
}
?>