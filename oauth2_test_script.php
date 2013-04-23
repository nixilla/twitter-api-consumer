<?php

// make sure that you run `composer install` before executing this script
// run it with php ./oauth2_test_script.php

require_once './vendor/autoload.php';

$consumer_key = 'your api key';
$consumer_secret = 'your api secret';

$client = new Buzz\Browser(new Buzz\Client\Curl());
$consumer = new Twitter\OAuth2\Consumer($client, $consumer_key, $consumer_secret);

$consumer->setConverter('/1.1/search/tweets.json', new \Twitter\TwitterSearchConverter());

try {

    $api_calls = 0;

    $query = $consumer->prepare(
        '/1.1/search/tweets.json',
        'GET',
        array('q' => urlencode('#twitterapi'), 'count' => 4, 'include_entities' => 1)
    );

    $result = $consumer->execute($query);
    $api_calls++;

    do
    {
        printf("Queried %s times, last time found %s tweets\n", $api_calls, count($result));

        $metainfo = $result->getMetainfo();

        echo $metainfo['next_results']. "\n";

        if($api_calls > 4) break;
    }
    while($consumer->execute($result->nextQuery()) && $api_calls++);
}
catch (Exception $e)
{
    printf('%s: %s', get_class($e), $e->getMessage());
    echo "\n";
}
