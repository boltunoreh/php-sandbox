<?php
$arrays = [];
$res = [];
echo "Start" . PHP_EOL;

echo "Make array" . PHP_EOL;
for ($j = 0; $j < 100000; $j++) {
    $texts[] = bin2hex(random_bytes(1000));
}

echo "Start line-by-line" . PHP_EOL;

$f = fopen('line-by-line.txt', 'w');
$startTime['line-by-line'] = microtime(true);
foreach ($texts as $text) {
    fwrite($f, $text);
}
$endTime['line-by-line'] = microtime(true);
fclose($f);

echo "Finish line-by-line" . PHP_EOL;
echo "Start batch" . PHP_EOL;

$f = fopen('batch.txt', 'w');
$startTime['batch'] = microtime(true);
$string = '';
foreach ($texts as $text) {
    $string .= $text;
}
fwrite($f, $string);
$endTime['batch'] = microtime(true);
fclose($f);

echo "Finish batch" . PHP_EOL;

### Echo results
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}