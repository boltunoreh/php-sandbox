<?php

$startTime['array_pop'] = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    $a = range(1, 100);
    $r = array_pop($a);
}
$endTime['array_pop'] = microtime(true);

$startTime['count-1'] = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    $a = range(1, 100);
    $r = array_values($a)[count($a) - 1];
}
$endTime['count-1'] = microtime(true);


### Echo results
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}
