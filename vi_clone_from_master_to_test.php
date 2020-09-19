<?php

$testDb = new PDO(
    'mysql:dbname=vseins;host=10.241.74.118;port=3043',
    'vseinsrv1',
    'phub4Aig'
);
$masterDb = new PDO(
    'mysql:dbname=vseins;host=10.250.20.103;port=3306',
    'virouser',
    'aokjJh654dea'
);

$tables = [
    'vseins_orders',
    'vseins_orders_payments',
    'vseins_orders_contragents',
];
$chunk = 1000;

foreach ($tables as $table) {
    echo "Process table {$table}" . PHP_EOL;
    echo "========================================" . PHP_EOL;

    $sql = "
        SELECT *
        FROM {$table}
        ORDER BY id DESC
        LIMIT 1
        ;
    ";
    $result = $testDb->query($sql);

    $row = $result->fetch(PDO::FETCH_ASSOC);
    $fields = implode(', ', array_keys($row));
    $lastId = $row['id'];

    $i = 0;
    while (true) {
        $offset = $chunk * $i;

        $sql = "
            SELECT {$fields}
            FROM {$table}
            WHERE id > {$lastId}
            LIMIT {$offset}, {$chunk}
            ;
        ";

        $masterDbResult = $masterDb->query($sql);

        $rowsCount = 0;
        $insertIds = [];
        $insertValues = [];
        $sql = "
            INSERT INTO {$table} ({$fields}) VALUES 
        ";

        while ($row = $masterDbResult->fetch(PDO::FETCH_ASSOC)) {
            $row = array_map(function ($value) {
                if (is_numeric($value)) {
                    return $value;
                }

                if (is_string($value)) {
                    return "'" . $value . "'";
                }

                if (is_null($value)) {
                    return 'NULL';
                }

                return $value;
            }, $row);

            $values = implode(', ', $row);

            $insertValues[] = "({$values})";
            $insertIds[] = $row['id'];

            $rowsCount++;
        }

        $sql = $sql . implode(', ', $insertValues) . ";";

        try {
            $result = $testDb->query($sql);

            if (PDO::ERR_NONE != $testDb->errorCode()) {
                echo "FAILED!!!. Rows " . implode(', ', $insertIds) . " not cloned!!! ERROR: " . json_encode($testDb->errorInfo()) . PHP_EOL;
            } else {
                echo "SUCCESS. Rows " . implode(', ', $insertIds) . " cloned" . PHP_EOL;
            }
        } catch (Exception $e) {
            echo "!!!EXCEPTION!!!. Rows " . implode(', ', $insertIds) . " not cloned!!! ERROR: " . json_encode($e->getMessage()) . PHP_EOL;
        }

        if ($rowsCount < $chunk) {
            break;
        }

        $i++;
    }

    echo "+++++++++ TABLE PROCESSED!!! +++++++++" . PHP_EOL;
}