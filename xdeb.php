<?php

$mysqli = new mysqli("localhost", "root", "root", "vi_sandbox");
$errno = mysqli_connect_errno();
if ($errno) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
}

$query = 'I AM FUCKING XDEBUG-BUG;';
$result = $mysqli->query($query);
echo 'errno = ' . $mysqli->errno . PHP_EOL;
echo 'error = ' . $mysqli->error . PHP_EOL;
$mock = 'Some action for the illustration of the problem.';
echo 'errno = ' . $mysqli->errno . PHP_EOL;
echo 'error = ' . $mysqli->error . PHP_EOL;