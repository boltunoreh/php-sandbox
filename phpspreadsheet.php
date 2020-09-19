<?php

require __DIR__ . '/vendor/autoload.php';

$inputFileName = './excel95.xls';

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$spreadsheet = $reader->load($inputFileName);
$worksheet = $spreadsheet->getActiveSheet();

foreach ($worksheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(FALSE);
    foreach ($cellIterator as $cell) {
        echo $cell->getValue() . PHP_EOL;
    }
}
