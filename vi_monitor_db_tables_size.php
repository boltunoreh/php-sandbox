<?php

$localDb = new PDO(
    'mysql:dbname=vi_sandbox;host=127.0.0.1;port=3306',
    'root',
    'root'
);
$replicaDb = new PDO(
    'mysql:dbname=vseins;host=10.250.20.103;port=3306',
    'virouser',
    'aokjJh654dea'
);

$sql = 'CREATE TABLE IF NOT EXISTS table_size_monitoring
            (
                id         int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                table_name VARCHAR(255)        NOT NULL,
                rows    INT(11)               NOT NULL,
                size_mb    FLOAT               NOT NULL,
                created_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
            ENGINE = InnoDB
            CHARSET = UTF8
        ;';
$dbResult = $localDb->query($sql);

$sql = 'SELECT
           table_name AS table_name,
           round(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
           TABLE_ROWS as rows
        FROM information_schema.TABLES
        WHERE table_name IN
            (
               "vseins_goods_availability",
               "vseins_kits_goods",
               "vseins_rnames",
               "vseins_kits_represent_available",
               "vseins_goods_rashodka",
               "vseins_goods_rashodka_temp",
               "vseins_good_price",
               "vseins_orders"
            )
        ORDER BY (data_length + index_length) DESC;';

$dbResult = $replicaDb->query($sql);
while ($row = $dbResult->fetch(PDO::FETCH_ASSOC)) {
    $rows[] = "(\"{$row['table_name']}\", {$row['rows']}, {$row['size_mb']})";
}

$values = implode(',', $rows);

$sql = "
    INSERT INTO
        table_size_monitoring (table_name, rows, size_mb)
    VALUES
        {$values}
    ;
";
$dbResult = $localDb->query($sql);