Twitter API Consumer
====================

|Travis|_

.. |Travis| image:: https://travis-ci.org/nixilla/twitter-api-consumer.png?branch=master
.. _Travis: https://travis-ci.org/nixilla/twitter-api-consumer

This is the small library to make calls to Twitter API. It uses `kriswallsmith/buzz`_ for HTTP.

.. _kriswallsmith/buzz: https://github.com/kriswallsmith/Buzz

Twitter API supports OAuth2 but for `application-only authentication`_.
This library currently supports OAuth2, but there are plans for OAuth1a too.

.. _`application-only authentication`: https://dev.twitter.com/docs/auth/application-only-auth

The concept for this library is to use Twitter API with as small as 4 lines of code

.. code:: php

    <?php

    $client = new Buzz\Browser(new Buzz\Client\Curl());
    $consumer = new Twitter\OAuth2\Consumer($client, $consumer_key, $consumer_secret);
    $query = $consumer->prepare('/1.1/search/tweets.json','GET', array('q' => '#twitterapi'));
    $result = $consumer->execute($query);

If you're using Symfony2 and Dependecy Injection you can even do this is 3 lines

.. code:: php

    <?php

    $consumer = $this->container->get('twitter.consumer');
    $query = $consumer->prepare('/1.1/search/tweets.json','GET', array('q' => '#twitterapi'));
    $result = $consumer->execute($query);

By default the $result variable contains Result object implementing ArrayAccess, Countable, Iterator interfaces,
with the structure equivalent to json response from the Twitter API. It gets it from DefaultConverter class.
However you can change it, by using converters. Converter is the special class that implements ConverterInterface
with just one method "convert". It gets raw input as a parameter, which by default is json string.

You can inject converter class for given API method into $consumer object like this:

.. code:: php

    <?php

    $consumer->setConverter('/1.1/search/tweets.json', new \TwitterSearchConverter());

And converter class is very simple and can look like this:

.. code:: php

    <?php

    class TwitterSearchConverter implements ConverterInterface
    {
        public function convert($input)
        {
            $result = json_decode($input, true);

            return array('data' => $result['statuses'], 'metainfo' => $result['search_metadata']);
        }
    }

Of course you can do more complicated conversion, like creating and persisting database objects and return for example
Doctrine ArrayCollection.

The whole point of converter class is to externalize data conversion from one format to another
and give you control over it. You can (and you should) inject converters via DI in Symfony2 like this
(this is just an example):

.. code:: yaml

    twitter.consumer:
        class: %twitter.oauth2.consumer.class%
        arguments: [ @buzz, %twitter.app_id%, %twitter.secret% ]
        calls:
            - [ setConverter , ["/1.1/search/tweets.json", "@tweet.converter" ] ]


You can also paginate over the result. All you need to do is to make sure that your converter class returns following key:

.. code:: php

    <?php

    $result = $converter->convert($json_string);
    assertNotNull($result['metainfo']['next_results']);

The class TwitterSearchConverter above is the example converter which you can use for result pagination. This is how you do it:

.. code:: php

    <?php

    $query = $consumer->prepare('/1.1/search/tweets.json','GET', array('q' => '#twitterapi'));
    $api_calls = 0;

    $result = $consumer->execute($query);
    $api_calls++;

    do
    {
        printf("Queried %s times, last time found %s tweets\n", $api_calls, count($result));

        foreach($result as $key => $tweet)
            echo $tweet['text'] . "\n";
    }
    while(($result = $consumer->execute($result->nextQuery())) && $api_calls++);

Installation
============

Via composer (don't use 0.2 - it's broken).

.. code-block:: json

    {
        "require": {
            "nixilla/twitter-api-consumer": "~0.3"
        }
    }


Tests
=====

This is copy/paste command

.. code:: sh

    git clone https://github.com/nixilla/twitter-api-consumer.git && \
    cd twitter-api-consumer && \
    mkdir bin && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=bin && \
    ./bin/composer.phar install --dev && \
    ./bin/phpunit

