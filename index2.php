<?php
private function callApi( $method, $params)
{
    $url = sprintf(
        "https://api.telegram.org/bot%s/%s",
        Config::get('telegram.token'),
        $method
    );

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_FOLLOWLOCATION => FALSE,
        CURLOPT_HEADER => FALSE,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => array('Accept-Language: ru,en-us'),
        CURLOPT_POSTFIELDS => $params,

    ));

    $response = curl_exec($ch);
    return json_decode($response);
}

    $this->callApi('sendMessage', array(
        'chat_id' => $data->message->chat->id,
        'text' => "Здесь сообщение от нашего бота",
        //  'reply_to_message_id'   => $data->message->message_id,
    ));

?>