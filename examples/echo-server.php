<?php

require __DIR__ . '/../src/Runner.php';

use PHProutine\Runner;

$server = stream_socket_server('tcp://127.0.0.1:8000');
$runner = new Runner;
while (true) {
    $client = stream_socket_accept($server, -1);
    $runner->go(function() use ($client) {
        stream_copy_to_stream($client, $client);
    });
    fclose($client);
}
