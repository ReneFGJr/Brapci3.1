<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use CodeIgniter\I18n\Time;
use CodeIgniter\I18n\TimeLegacy;

date_default_timezone_set('America/Sao_Paulo');
class ExtendedTimeForCompatibility extends Time {}
$cases = [
    [Time::class, 0, null, '1969-12-31 21:00:00.000000 -03:00'],
    [Time::class, 123.456789, 'UTC', '1970-01-01 00:02:03.456789 +00:00'],
    [Time::class, -0.25, 'UTC', '1969-12-31 23:59:59.750000 +00:00'],
    [Time::class, 0, new DateTimeZone('America/Sao_Paulo'), '1969-12-31 21:00:00.000000 -03:00'],
    [ExtendedTimeForCompatibility::class, 0, 'UTC', '1970-01-01 00:00:00.000000 +00:00'],
    [TimeLegacy::class, 0, 'UTC', '1970-01-01 00:00:00.000000 +00:00'],
];
foreach ($cases as [$class, $timestamp, $zone, $expected]) {
    $time = $class::createFromTimestamp($timestamp, $zone, 'pt_BR');
    $locale = (new ReflectionProperty($time, 'locale'))->getValue($time);
    if (!$time instanceof $class || $time->format('Y-m-d H:i:s.u P') !== $expected || $locale !== 'pt_BR') {
        throw new RuntimeException('Falhou: ' . $class . ' timestamp=' . $timestamp);
    }
}
echo 'OK PHP ' . PHP_VERSION . ': inteiros, frações, negativos, fusos, locale, subclasses e TimeLegacy.' . PHP_EOL;
