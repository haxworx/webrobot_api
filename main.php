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
        'botId' => $botId,
        'agent' => 'bot/1.0',
        'delay' => 1,
        'ignoreQuery' => true,
        'importSitemaps' => true,
        'retryMax' => 5,
        'startTime' => '14:00',
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
        'ignoreQuery' => true,
        'importSitemaps' => true,
        'retryMax' => 5,
        'startTime' => '13:00',
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

    $o = json_decode($content, false);
    if ((isset($o->message)) && ($o->message === 'ok')) {
        if (isset($o->botId)) {
            return intval($o->botId);
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
        'botId' => $botId,
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

    $o = json_decode($content, false);
    if ((isset($o->message)) && ($o->message === 'ok')) {
        return true;
    }

    return false;
}

function robotList()
{
    $uri = "http://localhost:8080/api/robot/query/all";
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

    $ids = [];
    $o = json_decode($content, false);
    foreach ($o as $bot) {
        $ids[] = $bot->botId;
    }

    return $ids;
}

function robotLaunches(int $botId)
{
    $uri = "http://localhost:8080/api/robot/query/launches";
    $headers = headersSet();
    $client = HttpClient::create();

    $obj = [
        'botId' => $botId,
    ];

    $response = $client->request('GET', $uri, [
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

    return $o;
}

function robotRecords(int $botId, int $launchId)
{
    $uri = "http://localhost:8080/api/robot/records/$botId/launch/$launchId/offset/0";
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
        echo "Error: ($statusCode) " . $e->getMessage() . "\n";
        return false;
    }

    $o = json_decode($content, false);

    return $o;
}

function robotRecord(int $botId, int $recordId)
{
    $uri = "http://localhost:8080/api/robot/records/download/$botId/record/$recordId";
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
        echo "Error: ($statusCode) " . $e->getMessage() . "\n";
        return false;
    }

    $o = json_decode($content, false);

    return $o;
}

function main($args)
{
    $launches = [];
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
    if (($bots = robotList()) !== false) {
        foreach ($bots as $botId) {
            echo "$botId\n";
            $l = robotLaunches($botId);
            $launches[] = $l;
        }
    } else {
        return 1;
    }

    foreach ($launches as $launch) {
        $launchId = $launch->id;
        $botId = $launch->botId;
        echo "$launchId $botId\n";
        $records = robotRecords($botId, $launchId);
        foreach ($records as $record) {
            $o = robotRecord($botId, $record->id);
            var_dump($o);
            break;
        }
        break;
    }

    return 0;
}

exit(main($argv));

?>
