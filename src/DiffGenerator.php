<?php

namespace DiffGenerator\DiffGenerator;

const INVALID_FILE_MESSAGE = '%s is invalid';
const INVALID_PATH_MESSAGE = "%s doesn't exists.";

function actualizePath(string $path): ?string
{
    if (file_exists($path)) {
        return $path;
    }

    $path = sprintf('%s/../%s', __DIR__, $path);

    return file_exists($path) ? $path : null;
}

/**
 * @throws \Exception
 */
function genDiff(string $firstFilePath, string $secondFilePath): void
{
    if (!$firstActualPath = actualizePath($firstFilePath)) {
        throw new \Exception(sprintf(INVALID_PATH_MESSAGE, $firstFilePath));
    }

    if (!$secondActualPath = actualizePath($secondFilePath)) {
        throw new \Exception(sprintf(INVALID_PATH_MESSAGE, $secondFilePath));
    }

    $firstContent = file_get_contents($firstActualPath);
    if (!$firstContent || !$firstJson = json_decode($firstContent, true)) {
        throw new \Exception(sprintf(INVALID_FILE_MESSAGE, $firstFilePath));
    }

    $secondContent = file_get_contents($secondActualPath);
    if (!$secondContent || !$secondJson = json_decode($secondContent, true)) {
        throw new \Exception(sprintf(INVALID_FILE_MESSAGE, $secondFilePath));
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

/**
 * @param mixed $value
 */
function valueToString($value): string
{
    return is_string($value) ? $value : var_export($value, true);
}
