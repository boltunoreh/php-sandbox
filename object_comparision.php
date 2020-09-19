<?php
$a = 123;

class Abc {
    /**
     * @var int
     */
    private $porpOne = 1;

    /**
     * @var int
     */
    private $porpTwo = 2;

    /**
     * @var int
     */
    private $porpThree = 3;

    /**
     * @return int
     */
    public function getPorpOne(): int
    {
        return $this->porpOne;
    }

    /**
     * @return int
     */
    public function getPorpTwo(): int
    {
        return $this->porpTwo;
    }

    /**
     * @return int
     */
    public function getPorpThree(): int
    {
        return $this->porpThree;
    }
}
$a = new Abc();
$b = new Abc();
$bInt = $b->getPorpOne();

$startTime['int'] = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
  $c = $bInt === $a->getPorpOne();
}
$endTime['int'] = microtime(true);

$startTime['object'] = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    $c = $b === $a;
}
$endTime['object'] = microtime(true);

### Echo results
foreach ($endTime as $type => $time) {
  echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
}
