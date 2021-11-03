<?php

namespace DiffHelper;

/**
 * @param object $struct
 *
 * @return array<int, string>
 */
function getExitingKeys(object $struct): array
{
    return array_merge(array_keys(get_object_vars($struct)));
}

/**
 * @param object $obj1
 * @param object $obj2
 *
 * @return array<array>
 */
function calculateDiff(object $obj1, object $obj2): array
{
    $keys = array_unique(array_merge(getExitingKeys($obj1), getExitingKeys($obj2)));
    natsort($keys);

    $diff = [];

    foreach ($keys as $key) {
        $val1 = property_exists($obj1, $key) ? valueToString($obj1->$key) : null;
        $val2 = property_exists($obj2, $key) ? valueToString($obj2->$key) : null;
        $diff[$key] = [$val1, $val2];
    }

    return $diff;
}

/**
 * @param mixed $value
 *
 * @return string
 */
function valueToString($value): string
{
    switch (gettype($value)) {
        case 'string':
            return $value;
        case 'array':
        case 'object':
            return json_encode($value) ?: '';
        case 'NULL':
            return 'null';
        case 'boolean':
            return $value ? 'true' : 'false';
        default:
            return (string) $value;
    }
}
