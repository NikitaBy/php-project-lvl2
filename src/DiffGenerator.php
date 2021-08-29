<?php

namespace DiffGenerator\DiffGenerator;

function gendiff($firstFilePath, $secondFilePath)
{
    try {
        $firstJson = file_exists($firstFilePath)
            ? json_decode(file_get_contents($firstFilePath), true)
            : json_decode(file_get_contents(__DIR__ . '/../' . $firstFilePath), true);
        $secondJson = file_exists($secondFilePath)
            ? json_decode(file_get_contents($secondFilePath), true)
            : json_decode(file_get_contents(__DIR__ . '/../' . $secondFilePath), true);
    } catch (\JsonException $e) {
        print_r($e->getMessage());

        return;
    }

    $result = [];
    foreach (array_keys($firstJson) as $key) {
        $result[$key] = [true, isset($secondJson[$key])];
    }

    foreach (array_diff_key($secondJson, $result) as $key => $value) {
        $result[$key] = [false, true];
    }

    ksort($result);

    print_r("{\n");
    foreach ($result as $key => [$exists1, $exists2]) {
        if ($exists1 && $exists2) {
            $val1 = $firstJson[$key];
            $val2 = $secondJson[$key];

            if ($val1 === $val2) {
                print_r(sprintf("    %s: %s\n", $key, valueToString($val1)));
            } else {
                print_r(sprintf("  - %s: %s\n", $key, valueToString($val1)));
                print_r(sprintf("  + %s: %s\n", $key, valueToString($val2)));
            }
        } elseif ($exists1) {
            $val1 = $firstJson[$key];
            print_r(sprintf("  - %s: %s\n", $key, valueToString($val1)));
        } else {
            $val2 = $secondJson[$key];
            print_r(sprintf("  + %s: %s\n", $key, valueToString($val2)));
        }
    }
    print_r("}\n");
}

function valueToString($value): ?string
{
    if (is_string($value)) {
        return sprintf('"%s"', $value);
    }

    return var_export($value, true);
}
