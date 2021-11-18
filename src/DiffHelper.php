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
    $sortedKey = collect($keys)->sort(SORT_NATURAL)->toArray();

    return array_reduce(
        $sortedKey,
        function ($diff, $key) use ($obj1, $obj2) {
            $val1 = property_exists($obj1, $key) ? json_encode($obj1->$key) : null;
            $val2 = property_exists($obj2, $key) ? json_encode($obj2->$key) : null;

            return array_merge($diff, [$key => [$val1, $val2]]);
        },
        []
    );
}
