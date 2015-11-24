#!/usr/bin/env php
<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../PHPFmt/PhpFormatterClient.php';
use PhpFormat\PhpFormatterClient;

$client = new PhpFormatterClient();
$client->run();
