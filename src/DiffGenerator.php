<?php

namespace DiffGenerator;

use function DiffGenerator\Parsers\JsonParser\parse as jsonParse;
use function DiffGenerator\Parsers\YamlParser\parse as yamlParse;

const INVALID_EXTENSION_MESSAGE = 'Extension "%s" is invalid.';
const INVALID_FILE_MESSAGE = '"%s" is invalid.';
const INVALID_PATH_MESSAGE = '"%s" doesn\'t exists.';

/**
 * @throws \Exception
 */
function parseContent(string $content, string $extension): ?array
{
    switch ($extension) {
        case ('json'):
            return jsonParse($content);
        case ('yaml'):
        case ('yml'):
            return yamlParse($content);
        default:
            throw new \Exception(sprintf(INVALID_EXTENSION_MESSAGE, $extension));
    }
}

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
function getContent(string $path): array
{
    if (!$actualPath = actualizePath($path)) {
        throw new \Exception(sprintf(INVALID_PATH_MESSAGE, $path));
    }

    if (!$content = file_get_contents($actualPath)) {
        throw new \Exception(sprintf(INVALID_FILE_MESSAGE, $actualPath));
    }

    $extension = pathinfo($actualPath, PATHINFO_EXTENSION);

    if (!$parseContent = parseContent($content, $extension)) {
        throw new \Exception(sprintf(INVALID_FILE_MESSAGE, $actualPath));
    }

    return $parseContent;
}

/**
 * @throws \Exception
 */
function genDiff(string $firstFilePath, string $secondFilePath): void
{
    $firstContent = getContent($firstFilePath);
    $secondContent = getContent($secondFilePath);

    $result = [];
    foreach (array_keys($firstContent) as $key) {
        $result[$key] = [true, isset($secondContent[$key])];
    }

    foreach (array_diff_key($secondContent, $result) as $key => $value) {
        $result[$key] = [false, true];
    }

    ksort($result);

    print_r("{\n");
    foreach ($result as $key => [$exists1, $exists2]) {
        if ($exists1 && $exists2) {
            $val1 = $firstContent[$key];
            $val2 = $secondContent[$key];

            if ($val1 === $val2) {
                print_r(sprintf("    %s: %s\n", $key, valueToString($val1)));
            } else {
                print_r(sprintf("  - %s: %s\n", $key, valueToString($val1)));
                print_r(sprintf("  + %s: %s\n", $key, valueToString($val2)));
            }
        } elseif ($exists1) {
            $val1 = $firstContent[$key];
            print_r(sprintf("  - %s: %s\n", $key, valueToString($val1)));
        } else {
            $val2 = $secondContent[$key];
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
