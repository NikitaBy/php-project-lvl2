<?php

declare(strict_types=1);

namespace DiffGenerator\Formatter\Json\JsonFormatter;

use function DiffHelper\calculateDiff;

const TYPE_ADDED = 'added';
const TYPE_REMOVED = 'removed';
const TYPE_UNCHANGED = 'unchanged';
const TYPE_UPDATED = 'updated';

function formatDiff(array $diff): string
{
    $result = formatToArray($diff);

    return sprintf("%s\n", json_encode($result));
}

/**
 * @param array $diff
 *
 * @return mixed
 */
function formatToArray(array $diff)
{
    return array_reduce(
        array_keys($diff),
        function ($acc, $key) use ($diff) {
            [$val1, $val2] = $diff[$key];

            if ($val1 === $val2) {
                $acc[] = getStructureUnchanged($key, $val1);

                return $acc;
            }

            if (is_null($val1)) {
                $acc[] = getStructureAdded($key, $val2);

                return $acc;
            }

            if (is_null($val2)) {
                $acc[] = getStructureRemoved($key, $val1);

                return $acc;
            }

            if (is_object($obj1 = json_decode($val1)) && is_object($obj2 = json_decode($val2))) {
                $acc[] = getStructureUnchanged($key, formatToArray(calculateDiff($obj1, $obj2)));

                return $acc;
            }

            $acc[] = getStructureUpdated($key, $val1, $val2);

            return $acc;
        },
        []
    );
}

function parseValue($value)
{
    $value = json_decode($value, true);

    return $value;
}

function getStructureAdded(string $key, $value): array
{
    return [
        'name'     => $key,
        'type'     => TYPE_ADDED,
        'newValue' => parseValue($value),
    ];
}

function getStructureRemoved(string $key, $value): array
{
    return [
        'name'     => $key,
        'type'     => TYPE_REMOVED,
        'oldValue' => parseValue($value),
    ];
}

function getStructureUnchanged(string $key, $value): array
{
    return [
        'name'  => $key,
        'type'  => TYPE_UNCHANGED,
        'value' => is_array($value) ? $value : parseValue($value),
    ];
}

function getStructureUpdated(string $key, $oldValue, $newValue): array
{
    return [
        'name'     => $key,
        'type'     => TYPE_UPDATED,
        'oldValue' => parseValue($oldValue),
        'newValue' => parseValue($newValue),
    ];
}
