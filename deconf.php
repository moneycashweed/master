<?php
error_reporting(E_ALL);
define('USE_WP_THEMES', false);
require("wp-load.php");

define(BOT_TOKEN, '269051566:AA214473630:AAHx0uBoSJ5_SqYdk8kIWAS0KmjunAt1x_A');
define(API_URL, "https://api.telegram.org/bot".BOT_TOKEN."/");

function apiRequestWebhook($method, $parameters)
{
    if (!is_string($method))
    {
        echo("Method name must be a string\n");
        return false;
    }

    if (!$parameters)
    {
        $parameters = array();
    }
    else if (!is_array($parameters))
    {
        echo("Parameters must be an array\n");
        return false;
    }

    $parameters["method"] = $method;

    header("Content-Type: application/json");
    echo json_encode($parameters);
    return true;
}

function exec_curl_request($handle)
{
    $response = curl_exec($handle);

    if ($response === false)
    {
        $errno = curl_errno($handle);
        $error = curl_error($handle);
        echo("Curl returned error $errno: $error\n");
        curl_close($handle);
        return false;
    }

    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);

    if ($http_code >= 500)
    {
        // do not wat to DDOS server if something goes wrong
        sleep(10);
        return false;
    }
    else if ($http_code != 200)
    {
        echo $response;
        $response = json_decode($response, true);

        echo("Request has failed with error {$response['error_code']}: {$response['description']}\n");
        if ($http_code == 401)
        {
            throw new Exception('Invalid access token provided');
        }
        return false;
    }
    else
    {
        $response = json_decode($response, true);
        if (isset($response['description']))
        {
            echo("Request was successfull: {$response['description']}\n");
        }
        $response = $response['result'];
    }

    return $response;
}

function apiRequest($method, $parameters)
{
    if (!is_string($method))
    {
        echo("Method name must be a string\n");
        return false;
    }

    if (!$parameters)
    {
        $parameters = array();
    }
    else if (!is_array($parameters))
    {
        echo("Parameters must be an array\n");
        return false;
    }

    foreach ($parameters as $key => &$val)
    {
        // encoding to JSON array parameters, for example reply_markup
        if (!is_numeric($val) && !is_string($val))
        {
            $val = json_encode($val);
        }
    }
    $url = API_URL . $method . '?' . http_build_query($parameters);
    //echo $url."\r\n";

    $handle = curl_init($url);
    //curl_setopt($handle, CURLOPT_HEADER, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);

    return exec_curl_request($handle);
}

function apiRequestJson($method, $parameters)
{
    if (!is_string($method))
    {
        echo("Method name must be a string\n");
        return false;
    }

    if (!$parameters)
    {
        $parameters = array();
    }
    else if (!is_array($parameters))
    {
        echo("Parameters must be an array\n");
        return false;
    }

    $parameters["method"] = $method;

    $handle = curl_init(API_URL);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
    curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

    return exec_curl_request($handle);
}


// "id":214473630

$args = ["orderby" => "comment_date", 'date_query' => ['after' =>'59 minutes ago','inclusive'=> true]];
$comments = get_comments($args);
//print_r($comments);die;
$posts = [];
foreach($comments as $c)
{
    if(in_array($c->comment_post_ID, $posts))
        continue;
    $posts[] = $c->comment_post_ID;
    $post = get_post($c->comment_post_ID);
    $msg .= "коммент от $c->comment_author:\n$c->comment_content\n$post->guid#comment$c->comment_ID\n\n";
}
if($msg)
{
    $msg = "Последние коменты:\n".$msg;
    $data = apiRequest("sendMessage", ["chat_id"=>175910036,"text"=>$msg]);
    //$data = apiRequest("sendMessage", ["chat_id"=>100810918,"text"=>$msg]);
    $data = apiRequest("sendMessage", ["chat_id"=>119394853,"text"=>$msg]);
    $data = apiRequest("sendMessage", ["chat_id"=>79162251,"text"=>$msg]);
}
else
{
    echo("no messages");
}

$args = ["orderby" => "post_date", 'date_query' => ['after' =>'59 minutes ago','inclusive'=> true]];
$posts = get_posts($args);

$done_posts = [];
$msg = '';
foreach($posts as $p)
{
    if(in_array($p->post_ID, $done_posts))
        continue;

    $done_posts[] = $p->post_ID;
    $post = get_post($p->post_ID);
    $author = get_user_by('id',$p->post_author);
    $msg .= "Пост от ".$author->data->user_nicename."\n$p->post_title\n$p->guid";
}
if($msg)
{
    $msg = "Последние посты:\n".$msg;
    $data = apiRequest("sendMessage", ["chat_id"=>175910036,"text"=>$msg]);
    //$data = apiRequest("sendMessage", ["chat_id"=>100810918,"text"=>$msg]);
    $data = apiRequest("sendMessage", ["chat_id"=>119394853,"text"=>$msg]);
    $data = apiRequest("sendMessage", ["chat_id"=>79162251,"text"=>$msg]);
}
else
{
    echo("no messages");
}