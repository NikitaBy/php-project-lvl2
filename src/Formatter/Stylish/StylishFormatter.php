<?php

declare(strict_types=1);

namespace Differ\Formatter\Stylish\StylishFormatter;

use Exception;

use function DiffHelper\calculateDiff;
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
 * @param array<array> $diff
 * @param int          $depth
 *
 * @return string
 */
function formatDiff(array $diff, int $depth = 0): string
{
    $result = array_reduce(
        array_keys($diff),
        function ($acc, $key) use ($diff, $depth) {
            [$val1, $val2] = $diff[$key];

            if ($val1 === $val2) {
                return sprintf('%s%s', $acc, getRowEqual($depth, $key, parseSingleValueToString($val1, $depth + 1)));
            }

            if (is_null($val2)) {
                return sprintf('%s%s', $acc, getRowRemove($depth, $key, parseSingleValueToString($val1, $depth + 1)));
            }

            if (is_null($val1)) {
                return sprintf(
                    '%s%s',
                    $acc,
                    getRowAdd($depth, $key, parseSingleValueToString($val2, $depth + 1))
                );
            }

            if (is_object($obj1 = json_decode($val1)) && is_object($obj2 = json_decode($val2))) {
                $innerDiff = calculateDiff($obj1, $obj2);

                return sprintf('%s%s', $acc, getRowEqual($depth, $key, formatDiff($innerDiff, $depth + 1)));
            }

            return sprintf(
                '%s%s%s',
                $acc,
                getRowRemove($depth, $key, parseSingleValueToString($val1, $depth + 1)),
                getRowAdd($depth, $key, parseSingleValueToString($val2, $depth + 1))
            );
        },
        "{\n"
    );

//    foreach ($diff as $key => [$val1, $val2]) {
//        if ($val1 === $val2) {
//            $result .= getRowEqual($depth, $key, parseSingleValueToString($val1, $depth + 1));
//            continue;
//        }
//
//        if (is_null($val2)) {
//            $result .= getRowRemove($depth, $key, parseSingleValueToString($val1, $depth + 1));
//            continue;
//        }
//
//        if (is_null($val1)) {
//            $result .= getRowAdd($depth, $key, parseSingleValueToString($val2, $depth + 1));
//            continue;
//        }
//
//        if (is_object($obj1 = json_decode($val1)) && is_object($obj2 = json_decode($val2))) {
//            $innerDiff = calculateDiff($obj1, $obj2);
//            $result .= getRowEqual($depth, $key, formatDiff($innerDiff, $depth + 1));
//            continue;
//        }
//
//        $result .= getRowRemove($depth, $key, parseSingleValueToString($val1, $depth + 1));
//        $result .= getRowAdd($depth, $key, parseSingleValueToString($val2, $depth + 1));
//    }
//
//    $result .= sprintf('%s}', getOffset($depth));

    return sprintf('%s%s}', $result, getOffset($depth));
}

/**
 * @param string $value
 * @param int    $depth
 *
 * @return string
 * @throws Exception
 */
function parseSingleValueToString(string $value, int $depth = 0): string
{
    $decodedValue = json_decode($value, true);

    switch (gettype($decodedValue)) {
        case 'array':
        case 'object':
            $result = array_reduce(
                array_keys($decodedValue),
                function ($acc, $key) use ($decodedValue, $depth) {
                    $object = json_encode($decodedValue[$key]);

                    if ($object === false) {
                        throw new Exception();
                    }

                    return sprintf(
                        '%s%s',
                        $acc,
                        getRowEqual($depth, $key, parseSingleValueToString($object, $depth + 1))
                    );
                },
                "{\n"
            );

            return sprintf('%s%s', $result, sprintf('%s}', getOffset($depth)));
        case 'boolean':
            return is_bool($decodedValue) && $decodedValue ? 'true' : 'false';
        case 'NULL':
            return 'null';
        default:
            return (string) $decodedValue;
    }
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
