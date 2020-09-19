<?php
$ints = [];
for ($i = 0; $i < 10000000; $i++) {
    $ints[] = $i;
}

### foreach
$startTime['foreach'] = microtime(true);
foreach ($ints as $int) {
    $strings[] = (string) $int;
}
$endTime['foreach'] = microtime(true);

### array_map
$startTime['array_map'] = microtime(true);
$strings = array_map('strval', $ints);
$endTime['array_map'] = microtime(true);

### Echo results
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}