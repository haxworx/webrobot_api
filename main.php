<?php

require 'vendor/autoload.php';

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;

const API_KEY = "0bbf6bd1-285a-446b-a79d-1ead2457c210";

function headersSet()
{
    return [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-AUTH-TOKEN' => API_KEY,
    ];
}

function robotEdit(int $botId)
{
    $uri = "http://localhost:8080/api/robot/schedule/edit";
    $headers = headersSet();

    $client = HttpClient::create();

    $obj = [
        'bot_id' => $botId,
        'agent' => 'bot/1.0',
        'delay' => 1,
        'ignore_query' => true,
        'import_sitemaps' => true,
        'retry_max' => 5,
        'start_time' => '14:00',
    ];

    $response = $client->request('PATCH', $uri, [
        'headers' => $headers,
        'max_redirects' => 1,
        'json' => [ $obj ],
    ]);

    $statusCode = $response->getStatusCode();
    try {
        $content = $response->getContent();
    } catch (Exception $e) {
        echo "Error : ($statusCode) " . $e->getMessage() . "\n";
        return false;
    }

    $o = json_decode($content, false);
    return true;
}

function robotSchedule()
{
    $uri = "http://localhost:8080/api/robot/schedule";
    $headers = headersSet();

    $client = HttpClient::create();

    $obj = [
        'address' => 'http://google.com',
        'agent' => 'bot/1.0',
        'delay' => 1,
        'ignore_query' => true,
        'import_sitemaps' => true,
        'retry_max' => 5,
        'start_time' => '13:00',
    ];

    $response = $client->request('POST', $uri, [
        'headers' => $headers,
        'max_redirects' => 1,
        'json' => [ $obj ],
    ]);

    $statusCode = $response->getStatusCode();
    try {
        $content = $response->getContent();
    } catch (Exception $e) {
        echo "Error : ($statusCode) " . $e->getMessage() . "\n";
        return false;
    }

    $o = json_decode($content, true);
    if ((array_key_exists('message', $o)) && ($o['message'] === 'ok')) {
        if (array_key_exists('bot_id', $o)) {
            return intval($o['bot_id']);
        }
    }
    return false;
}

function robotDelete($botId)
{
    $uri = "http://localhost:8080/api/robot/schedule/remove";
    $headers = headersSet();
    $client = HttpClient::create();

    $obj = [
        'bot_id' => $botId,
    ];

    $response = $client->request('DELETE', $uri, [
        'headers' => $headers,
        'max_redirects' => 1,
        'json' => [ $obj ],
    ]);

    $statusCode = $response->getStatusCode();
    try {
        $content = $response->getContent();
    } catch (Exception $e) {
        echo "Error: ($statusCode) " . $e->getMessage() . "\n";
        return false;
    }

    $o = json_decode($content, true);
    if ((array_key_exists('message', $o)) && ($o['message'] === 'ok')) {
        return true;
    }

    return false;
}

function robotList()
{
    $uri = "http://localhost:8080/api/robot/";
    $headers = headersSet();
    $client = HttpClient::create();

    $response = $client->request('GET', $uri, [
        'headers' => $headers,
        'max_redirects' => 1,
    ]);

    $statusCode = $response->getStatusCode();
    try {
        $content = $response->getContent();
    } catch (Exception $e) {
        echo "Error : ($statusCode) " . $e->getMessage() . "\n";
        return false;
    }

    $o = json_decode($content, false);

    return true;
}

function main($args)
{
    if (($botId = robotSchedule()) !== false) {
        echo "Robot scheduled\n";
    } else {
        return 1;
    }
    if ((robotEdit($botId)) === true) {
        echo "Robot edited!\n";
    } else {
        return 1;
    }
    if ((robotDelete($botId)) === true) {
        echo "Robot deleted!\n";
    } else {
        return 1;
    }
    robotList();

    return 0;
}

exit(main($argv));

?>
