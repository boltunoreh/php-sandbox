<?php
$a = 123;

$startTime['==='] = microtime(true);
for ($i = 0; $i < 100000000; $i++) {
  $b = $a === 0;
}
$endTime['==='] = microtime(true);

$startTime['<='] = microtime(true);
for ($i = 0; $i < 100000000; $i++) {
    $b = $a <= 0;
}
$endTime['<='] = microtime(true);

### Echo results
foreach ($endTime as $type => $time) {
  echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}
