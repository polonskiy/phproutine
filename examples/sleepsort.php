<?php

//usage: php sleepsort.php 3 2 1 4

require __DIR__ . '/../src/Channel.php';
require __DIR__ . '/../src/Runner.php';

use PHProutine\Channel;
use PHProutine\Runner;

$runner = new Runner;
$channel = new Channel;

array_shift($argv);
foreach ($argv as $val) {
    $runner->go(function($v) use ($channel) {
        sleep($v);
        $channel->write($v);
    }, $val);
}

foreach ($argv as $val) {
    echo $channel->read(), "\n";
}
