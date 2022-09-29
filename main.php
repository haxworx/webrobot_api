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

    $jsonContent = json_decode($content, false);
    var_dump($jsonContent);
    return true;
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

    $jsonContent = json_decode($content, false);
    var_dump($jsonContent);

    return true;
}

function main($args)
{
//    robotSchedule();
    robotList();

    return 0;
}

exit(main($argv));

?>
