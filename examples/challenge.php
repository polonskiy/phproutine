<?php

require __DIR__ . '/../src/Channel.php';
require __DIR__ . '/../src/Runner.php';

use PHProutine\Channel;
use PHProutine\Runner;

// intDoubler doubles the given int, then sends it through the given channel
function intDoubler($ch, $n) {
    $ch->write($n * 2);
}

$runner = new Runner;
// Make channels
$ch = new Channel;
$answer = new Channel;

// Spawn 3 PHProutines (basically PROCESSES) to process data in background
$runner->go('intDoubler', $ch, 10);
$runner->go('intDoubler', $ch, 20);
$runner->go(function($a, $b) use ($ch) { $ch->write($a + $b); }, 30, 40);

// Create anonymous function on the fly, launch as PHProutine!
$runner->go(function() use ($ch, $answer) {
    // Save the 3 values passed through the channel as x, y, and z
    list($x, $y, $z) = [$ch->read(), $ch->read(), $ch->read()];
    // Calculate answer, write to `answer` channel
    $answer->write(sprintf('%d + %d + %d = %d', $x, $y, $z, $x + $y + $z));
});

// Print answer resulting from channel read
printf("%s\n", $answer->read());
