<?php

namespace VI\Utilities;

/**
 * Служебный класс для профилирования кода без доп либ
 *
 * позволяет замерять время работы участков кода, потребляемую память,
 * считать кол-во отрабатываний участков кода
 *
 * Для построения древовидной статистики (для замера времени работы/потребляемой памяти
 * разных частей метода и/или вложенных методов) в названии таймера используется
 * разделитель self::DELIMITER. Пример:
 *
 * public function parentMethod()
 * {
 *     VIStatistics::startTimer('parentMethod');
 *     VIStatistics::startTimer('parentMethod:part1');
 *     // some simple code
 *     VIStatistics::stopTimer('parentMethod:part1');
 *
 *     VIStatistics::startTimer('parentMethod:childMethod');
 *     $this->childMethod();
 *     VIStatistics::stopTimer('parentMethod:childMethod');
 *
 *     VIStatistics::startTimer('parentMethod:final');
 *     // some simple code
 *     VIStatistics::stopTimer('parentMethod:final');
 *     VIStatistics::stopTimer('parentMethod');
 * }
 *
 * public function childMethod()
 * {
 *     VIStatistics::startTimer('parentMethod:childMethod:part1');
 *     // some simple code
 *     VIStatistics::stopTimer('parentMethod:childMethod:part1');
 *
 *     VIStatistics::startTimer('parentMethod:childMethod:anotherMethod');
 *     SomeClass::anotherMethod();
 *     VIStatistics::stopTimer('parentMethod:childMethod:anotherMethod');
 * }
 *
 * Для вывода результата используется getTotal():
 *
 * echo jsonEncode(VIStatistics::getTotal(VIStatistics::TYPE_TIME));
 *
 * {
 *
 * }
 */
class VIStatistics
{
    const TYPE_COUNT = 'count';
    const TYPE_TIME = 'time';
    const TYPE_MEMORY_USAGE = 'memory_usage';

    const TOTAL = 'total';
    const DELIMITER = ':';

    /** @var array */
    private static $timerStart = [];

    /** @var array */
    public static $timerTotal = [];

    /** @var array */
    private static $memoryUsageStart = [];

    /** @var array */
    private static $memoryUsageTotal = [];

    /** @var array */
    public static $counter = [];

    /**
     * Обнулить всю статистику
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$timerStart = [];
        self::$timerTotal = [];
        self::$counter = [];
        self::$memoryUsageStart = [];
        self::$memoryUsageTotal = [];
    }

    /**
     * Стартовать таймер
     *
     * @param string $name
     *
     * @return void
     */
    public static function startTimer(string $name): void
    {
        self::$timerStart[$name] = microtime(TRUE);
    }

    /**
     * Остановить таймер
     *
     * @param string $name
     *
     * @return void
     */
    public static function stopTimer(string $name): void
    {
        if (isset(self::$timerStart[$name])) {
            self::$timerTotal[$name] = self::$timerTotal[$name] ?? 0;
            self::$timerTotal[$name] += microtime(TRUE) - self::$timerStart[$name];
        }
    }

    /**
     * Стартовать замер потребляемой памяти
     *
     * @param string $name
     */
    public static function startMemoryUsage(string $name): void
    {
        self::$memoryUsageStart[$name] = memory_get_usage();
    }

    /**
     * Остановить замер потребляемой памяти
     *
     * @param string $name
     */
    public static function stopMemoryUsage(string $name): void
    {
        self::$memoryUsageTotal[$name] = self::$memoryUsageTotal[$name] ?? 0;
        self::$memoryUsageTotal[$name] += memory_get_usage() - self::$memoryUsageStart[$name];
    }

    /**
     * Счётчик чего-либо (кол-во вызовов метода и т.п.)
     *
     * @param string|array $name
     */
    public static function countOne($name): void
    {
        if (is_array($name)) {
            foreach ($name as $item) {
                self::$counter[$item]++;
            }
        } else {
            self::$counter[$name]++;
        }
    }

    /**
     * Увеличение счётчика на заданное число
     *
     * @param string $name
     * @param int $count
     */
    public static function countAdd(string $name, int $count): void
    {
        self::$counter[$name] += $count;
    }

    /**
     * Вызывается в начале работы скрипта для инициализации ряда счётчиков
     *
     * @param array $names
     */
    public static function countClear(array $names): void
    {
        foreach ($names as $type) {
            self::$counter[$type] = 0;
        }
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public static function getTotal(string $name): array
    {
        $result = [];

        switch ($name) {
            case self::TYPE_TIME:
                $stats = self::$timerTotal;
                break;
            case self::TYPE_MEMORY_USAGE:
                $stats = self::$memoryUsageTotal;
                break;
            case self::TYPE_COUNT:
                $stats = self::$counter;
                break;
            default:
                $stats = [];
                break;
        }

        krsort($stats);

        foreach ($stats as $path => $stat) {
            $path .= self::DELIMITER . self::TOTAL;
            $path_parts = explode(self::DELIMITER, $path);

            $res = self::structurize($path_parts, $stat);
            $result = array_merge_recursive($result, $res);
        }

        $result = self::removeTotals($result);

        return $result;
    }

    /**
     * Формирует древовидный массив
     *
     * @param array $levels
     * @param $value
     *
     * @return array
     */
    private static function structurize(array $levels, $value): array
    {
        $level = array_shift($levels);

        if (count($levels) === 0) {
            $result[$level] = number_format($value, 2, '.', ' ');
        } else {
            $result[$level] = self::structurize($levels, $value);
        }

        return $result;
    }

    /**
     * Убирает лишние итоговые значения total для массивов с одним элементом
     *
     * @param array|string $source
     *
     * @return mixed
     */
    private static function removeTotals($source)
    {
        $result = [];

        if (!is_array($source)) {
            return $source;
        }

        if (1 === count($source)
            && isset($source[self::TOTAL])
        ) {
            $result = $source[self::TOTAL];
        } else {
            foreach ($source as $key => $sub_array) {
                $result[$key] = self::removeTotals($sub_array);
            }
            ksort($result);
        }

        return $result;
    }
}
