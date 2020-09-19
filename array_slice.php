<?php
for ($i = 1; $i < 6; $i++) {
    $source = range(0, $i);

    $slice = array_slice(array_slice($source, 0, -1), -2, 2);

    echo $i + 1 . ': ' . json_encode($source) . ' => ' . json_encode($slice) . PHP_EOL;
}