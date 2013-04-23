Twitter API Consumer
====================

Currently supports OAuth2 application authentication only.

.. image:: https://travis-ci.org/nixilla/twitter-api-consumer.png?branch=master

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

