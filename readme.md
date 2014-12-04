# PHProutine

PHProutine is a gouroutines emulation in PHP.
Inspired by Golang and https://gist.github.com/elimisteve/4442820

# Examples

## Goroutines

```go
// Steve Phillips / elimisteve
// 2013.01.03

package main

import "fmt"

// intDoubler doubles the given int, then sends it through the given channel
func intDoubler(ch chan int, n int) {
    ch <- n*2
}

func main() {
    // Make channel of ints
    ch := make(chan int)
    answer := make(chan string)

    // Spawn 3 goroutines (basically threads) to process data in background
    go intDoubler(ch, 10)
    go intDoubler(ch, 20)
    go func(a, b int) { ch <- a+b }(30, 40) // Take 2 ints, write sum to `ch`

    // Create anonymous function on the fly, launch as goroutine!
    go func() {
        // Save the 3 values passed through the channel as x, y, and z
        x, y, z := <-ch, <-ch, <-ch
        // Calculate answer, write to `answer` channel
        answer <- fmt.Sprintf("%d + %d + %d = %d", x, y, z, x+y+z)
    }()

    // Print answer resulting from channel read
    fmt.Printf("%s\n", <-answer)
}
```

## PHProutines

```php
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
```

## Echo server

```php
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
```
