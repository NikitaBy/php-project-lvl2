<?php

declare(strict_types=1);

namespace Differ\Formatter\Plain\PlainFormatter;

use function DiffHelper\calculateDiff;

const ROW_ADD = "Property '%s' was added with value: %s\n";
const ROW_REMOVE = "Property '%s' was removed\n";
const ROW_UPDATE = "Property '%s' was updated. From %s to %s\n";

const COMPLEX_VALUE_PLACEHOLDER = '[complex value]';

/**
 * @param array<array> $diff
 * @param string       $propertyPath
 *
 * @return string
 */
function formatDiff(array $diff, string $propertyPath = ''): string
{
    $result = array_reduce(
        array_keys($diff),
        function ($result, $key) use ($diff, $propertyPath) {
            [$val1, $val2] = $diff[$key];

            $currentPath = $propertyPath ? sprintf('%s.%s', $propertyPath, $key) : $key;

            if ($val1 === $val2) {
                return $result;
            }

            if (is_null($val2)) {
                return sprintf('%s%s', $result, getRowRemove($currentPath));
            }

            if (is_null($val1)) {
                return sprintf('%s%s', $result, getRowAdd($currentPath, $val2));
            }

            if (is_object($obj1 = json_decode($val1)) && is_object($obj2 = json_decode($val2))) {
                $innerDiff = calculateDiff($obj1, $obj2);

                return sprintf('%s%s', $result, formatDiff($innerDiff, $currentPath));
            }

            return sprintf('%s%s', $result, getRowUpdate($currentPath, $val1, $val2));
        },
        ''
    );

    return $propertyPath ? $result : rtrim($result);
}

/**
 * @param string $key
 * @param string $val2
 *
 * @return string
 */
function getRowAdd(string $key, string $val2): string
{
    return sprintf(ROW_ADD, $key, parseSingleValueToString($val2));
}

/**
 * @param string $key
 * @param string $oldValue
 * @param string $newValue
 *
 * @return string
 */
function getRowUpdate(string $key, string $oldValue, string $newValue): string
{
    return sprintf(ROW_UPDATE, $key, parseSingleValueToString($oldValue), parseSingleValueToString($newValue));
}

/**
 * @param string $key
 *
 * @return string
 */
function getRowRemove(string $key): string
{
    return sprintf(ROW_REMOVE, $key);
}

function parseSingleValueToString(string $value): string
{
    $decodedValue = json_decode($value, true);

    switch (gettype($decodedValue)) {
        case 'array':
            return COMPLEX_VALUE_PLACEHOLDER;
        case 'string':
            return "'{$decodedValue}'";
        case 'NULL':
            return 'null';
        case 'boolean':
            return $decodedValue ? 'true' : 'false';
        default:
            return (string) $decodedValue;
    }
}
