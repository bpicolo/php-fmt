#!/usr/bin/env php
<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../PHPAutoFormat/PhpFormatterClient.php';
use PhpAutoFormat\PhpFormatterClient;

$client = new PhpFormatterClient();
$client->run();
