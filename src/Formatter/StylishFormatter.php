<?php

declare(strict_types=1);

namespace DiffGenerator\Formatter\StylishFormatter;

use function DiffHelper\calculateDiff;
use function DiffHelper\valueToString;
use function sprintf;

const PREFIX_EQUALS = '    ';
const PREFIX_ADD = '  + ';
const PREFIX_REMOVE = '  - ';
const TAB = '    ';

/**
 * @param int    $depth
 * @param string $key
 * @param string $value
 *
 * @return string
 */
function getRowEqual(int $depth, string $key, string $value): string
{
    return sprintf("%s%s%s: %s\n", getOffset($depth), PREFIX_EQUALS, $key, $value);
}

/**
 * @param int    $depth
 * @param string $key
 * @param string $value
 *
 * @return string
 */
function getRowAdd(int $depth, string $key, string $value): string
{
    return sprintf("%s%s%s: %s\n", getOffset($depth), PREFIX_ADD, $key, $value);
}

/**
 * @param int    $depth
 * @param string $key
 * @param string $value
 *
 * @return string
 */
function getRowRemove(int $depth, string $key, string $value): string
{
    return sprintf("%s%s%s: %s\n", getOffset($depth), PREFIX_REMOVE, $key, $value);
}

/**
 * @param array $diff
 * @param int   $depth
 *
 * @return string
 */
function formatDiff(array $diff, int $depth = 0): string
{
    $result = "{\n";

    foreach ($diff as $key => [$val1, $val2]) {
        if ($val1 === $val2) {
            $result .= getRowEqual($depth, $key, parseSingleValueToString($val1, $depth + 1));
            continue;
        }

        if (is_null($val2)) {
            $result .= getRowRemove($depth, $key, parseSingleValueToString($val1, $depth + 1));
            continue;
        }

        if (is_null($val1)) {
            $result .= getRowAdd($depth, $key, parseSingleValueToString($val2, $depth + 1));
            continue;
        }

        if (($obj1 = json_decode($val1)) && ($obj2 = json_decode($val2))) {
            $innerDiff = calculateDiff($obj1, $obj2);
            $result .= getRowEqual($depth, $key, formatDiff($innerDiff, $depth + 1));
            continue;
        }

        $result .= getRowRemove($depth, $key, parseSingleValueToString($val1, $depth + 1));
        $result .= getRowAdd($depth, $key, parseSingleValueToString($val2, $depth + 1));
    }

    $result .= getOffset($depth)."}";

    if (!$depth) {
        $result .= "\n";
    }

    return $result;
}

/**
 * @param string $value
 * @param int    $depth
 *
 * @return string
 */
function parseSingleValueToString(string $value, int $depth = 0): string
{
    $decodedValue = json_decode($value, true);

    if (!is_array($decodedValue)) {
        return valueToString($value);
    }

    $result = "{\n";

    foreach ($decodedValue as $key => $value) {
        $value = valueToString($value);
        $result .= getRowEqual($depth, $key, parseSingleValueToString($value, $depth + 1));
    }

    $result .= getOffset($depth).'}';

    return $result;
}

/**
 * @param int $depth
 *
 * @return string
 */
function getOffset(int $depth = 1): string
{
    return str_repeat(TAB, $depth);
}
