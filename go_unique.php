<?php

$numbers = [];
while($number = fgets(STDIN)){
    $numbers[] = $number;
}

echo current(array_diff($numbers, array_diff_assoc($numbers, array_unique($numbers))));

die;
### gen nums
$numbers = [];

for ($i = 1; $i <= 4000; $i++) {
    $numbers[] = $i;
    $numbers[] = $i;
}

shuffle($numbers);

$i = count($numbers);
unset($numbers[$i]);

### foreach
$startTime['foreach'] = microtime(true);
foreach ($numbers as $key => $number) {
    if (!in_array($number, array_diff_assoc($numbers, [$key => $number]))) {
        echo $number;
        break;
    }
}
$endTime['foreach'] = microtime(true);

$startTime['array_diff'] = microtime(true);
echo current(array_diff($numbers, array_diff_assoc($numbers, array_unique($numbers))));
$endTime['array_diff'] = microtime(true);

### Echo results
echo PHP_EOL;
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}