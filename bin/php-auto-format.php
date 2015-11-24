#!/usr/bin/env php
<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../PHPAutoFormat/PhpAutoFormatterClient.php';
use PhpAutoFormat\PhpAutoFormatterClient;

$client = new PhpAutoFormatterClient();
$client->run();
