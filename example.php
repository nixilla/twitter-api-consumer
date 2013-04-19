<?php

// make sure that you run `composer install` before executing this script
// run it with php ./example.php

use Buzz\Message\RequestInterface;

require_once './vendor/autoload.php';

$consumer_key = 'YOUR_KEY';
$consumer_secret = 'YOUR_SECRET';

$client = new Buzz\Browser(new Buzz\Client\Curl());
$consumer = new Twitter\OAuth2\Consumer($client, $consumer_key, $consumer_secret);

try {
    $result = $consumer->call('/1.1/search/tweets.json', RequestInterface::METHOD_GET, array('q' => urlencode('#bbcqt')));

    print_r($result);
}
catch (Exception $e)
{
    printf('%s: %s', get_class($e), $e->getMessage());
    echo "\n";
}
