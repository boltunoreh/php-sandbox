<?php
$arrays = [];
$res = [];
echo "Start" . PHP_EOL;

echo "Make array" . PHP_EOL;
for ($j = 0; $j < 100000; $j++) {
    $array = range(0, 2);
    shuffle($array);
    $arrays[] = $array;
}

echo "Start max" . PHP_EOL;

$startTime['max'] = microtime(true);
foreach ($arrays as $array) {
    $res['max'][] = max($array);
}
$endTime['max'] = microtime(true);

echo "Finish max" . PHP_EOL;
echo "Start rsort" . PHP_EOL;

$startTime['rsort'] = microtime(true);
foreach ($arrays as $array) {
    rsort($array);

    $res['rsort'][] = reset($array);
}
$endTime['rsort'] = microtime(true);

echo "Finish rsort" . PHP_EOL;

echo array_diff($res['max'], $res['rsort']) ? 'differs' : 'equals' . PHP_EOL;

### Echo results
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}