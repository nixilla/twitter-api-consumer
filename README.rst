Twitter API Consumer
====================

This is the small library to make calls to Twitter API. It uses `kriswallsmith/buzz` for HTTP.

Twitter API supports OAuth 1a as well as OAuth2 but for 'application-only authentication'_.
This library currently supports OAuth2, but there are plans for OAuth1a too.

.. _'application-only authentication': https://dev.twitter.com/docs/auth/application-only-auth

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


|Travis|_

.. |Travis| image:: https://travis-ci.org/nixilla/twitter-api-consumer.png?branch=master
.. _Travis: https://travis-ci.org/nixilla/twitter-api-consumer


Installation and Tests
======================

This is copy/paste command

.. code:: sh

    git clone https://github.com/nixilla/twitter-api-consumer.git && \
    cd twitter-api-consumer && \
    mkdir bin && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=bin && \
    ./bin/composer.phar install --dev && \
    ./bin/phpunit

