<?php
$a = null;

$startTime['is_null'] = microtime(true);
for ($i = 0; $i < 10000000; $i++) {
  $r = is_null($a);
}
$endTime['is_null'] = microtime(true);

$startTime['empty'] = microtime(true);
for ($i = 0; $i < 10000000; $i++) {
    $r = empty($a);
}
$endTime['empty'] = microtime(true);

$startTime['=== null'] = microtime(true);
for ($i = 0; $i < 10000000; $i++) {
    $r = $a === null;
}
$endTime['=== null'] = microtime(true);

### Echo results
foreach ($endTime as $type => $time) {
  echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}
