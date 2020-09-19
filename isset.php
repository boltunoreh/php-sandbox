<?php
$a = [];

$startTime['isset'] = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
  if (isset($a[$i])) {
  }
}
$endTime['isset'] = microtime(true);

$startTime['array_key_exists'] = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
  if (array_key_exists($i, $a)) {
  }
}
$endTime['array_key_exists'] = microtime(true);

$startTime['$a[$i]'] = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    if ($a[$i]) {
    }
}
$endTime['$a[$i]'] = microtime(true);

### Echo results
foreach ($endTime as $type => $time) {
  echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}
