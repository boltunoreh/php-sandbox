<?php

use VI\Utilities\VIStatistics;

require 'VIStatistics.php';

VIStatistics::startTimer('numbers');
VIStatistics::startMemoryUsage('foreach');

$target = null;
$numbers = [];

$handle = fopen("input.txt", "r");
if ($handle) {
    $lastNumber = null;

    while (!feof($handle)) {
        $buffer = fgets($handle, 16384);
        if ($target === null) {
            $target = (int) trim($buffer);
        } else {
            $buffer = $lastNumber . $buffer;

            $currentNumbers = explode(' ', trim($buffer));

            $lastNumber = array_pop($currentNumbers);

            foreach ($currentNumbers as $number) {
                if ($number < $target) {
                    if (isset($numbers[($target - $number)])) {
                        file_put_contents('output.txt', 1);
                        exit;
                    }

                    $numbers[$number] = (int) $number;
                }
            }
        }
    }
    fclose($handle);
}
file_put_contents('output.txt', 0);
VIStatistics::stopTimer('numbers');
echo 'Time: ' . json_encode(VIStatistics::getTotal(VIStatistics::TYPE_TIME)) . PHP_EOL;
print formatBytes(memory_get_peak_usage());
exit;

$numbers[] = $lastNumber;

//print formatBytes(memory_get_peak_usage());

//$lines = file('input.txt');
//$target = (int) trim($lines[0]);
//$numbers2 = explode(' ', $lines[1]);
//print formatBytes(memory_get_peak_usage());
//$numbers2 = array_filter($numbers2, function ($number) use ($target) {
//    return $number < $target;
//});
//
//sort($numbers);
//sort($numbers2);
//
//$diff = array_diff(array_unique($numbers), array_unique($numbers2));
//$diff2 = array_diff(array_unique($numbers2), array_unique($numbers));

VIStatistics::stopTimer('numbers');
VIStatistics::startTimer('foreach');

foreach ($numbers as $key => $number) {
    unset($numbers[$key]);

    if (isset($numbers[($target - $number)])) {
        file_put_contents('output.txt', 1);
        VIStatistics::stopTimer('foreach');
        VIStatistics::stopMemoryUsage('foreach');
        echo 'Time: ' . json_encode(VIStatistics::getTotal(VIStatistics::TYPE_TIME)) . PHP_EOL;
        echo 'Memory: ' . json_encode(VIStatistics::getTotal(VIStatistics::TYPE_MEMORY_USAGE)) . PHP_EOL;
        print formatBytes(memory_get_peak_usage());
        exit;
    }
}

file_put_contents('output.txt', 0);
VIStatistics::stopTimer('foreach');
VIStatistics::stopMemoryUsage('foreach');
echo 'Time: ' . json_encode(VIStatistics::getTotal(VIStatistics::TYPE_TIME)) . PHP_EOL;
echo 'Memory: ' . json_encode(VIStatistics::getTotal(VIStatistics::TYPE_MEMORY_USAGE)) . PHP_EOL;
print formatBytes(memory_get_peak_usage());
exit;

VIStatistics::stopTimer('foreach');
VIStatistics::stopMemoryUsage('foreach');
echo 'Time: ' . json_encode(VIStatistics::getTotal(VIStatistics::TYPE_TIME)) . PHP_EOL;
echo 'Memory: ' . json_encode(VIStatistics::getTotal(VIStatistics::TYPE_MEMORY_USAGE)) . PHP_EOL;



function formatBytes($bytes, $precision = 2) {
    $units = array("b", "kb", "mb", "gb", "tb");

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . " " . $units[$pow];
}

### gen nums
$target = 5;

$numbers = [];

for ($i = 1; $i <= 100000; $i++) {
    $numbers[] = $i;
}

shuffle($numbers);

$numbers = array_slice($numbers, count($numbers) / 2);

file_put_contents('input.txt', $target . PHP_EOL . implode(' ', $numbers));

//echo json_encode($numbers);
### foreach
$startTime['foreach'] = microtime(true);

$numbers = array_filter($numbers, function ($number) use ($target) {
    return $number < $target;
});

foreach (array_unique($numbers) as $number) {
    if (in_array(($target - $number), $numbers)) {
        echo 1;
        break;
    }
}

echo 0;
$endTime['foreach'] = microtime(true);

### Echo results
echo PHP_EOL;
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}