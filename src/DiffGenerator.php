<?php

namespace DiffGenerator;

use function DiffGenerator\Parsers\ParserRegistry\getParserByFileExtension;

const INVALID_FILE_MESSAGE = '"%s" is invalid.';
const INVALID_PATH_MESSAGE = '"%s" doesn\'t exists.';

function actualizePath(string $path): ?string
{
    if (file_exists($path)) {
        return $path;
    }

    $path = sprintf('%s/../%s', __DIR__, $path);

    return file_exists($path) ? $path : null;
}

function getContent(string $path)
{
    if (!$actualPath = actualizePath($path)) {
        throw new \Exception(sprintf(INVALID_PATH_MESSAGE, $path));
    }

    if (!$content = file_get_contents($actualPath)) {
        throw new \Exception(sprintf(INVALID_FILE_MESSAGE, $actualPath));
    }

    $parser = getParserByFileExtension(pathinfo($actualPath, PATHINFO_EXTENSION));

    if (!$parseContent = $parser($content)) {
        throw new \Exception(sprintf(INVALID_FILE_MESSAGE, $actualPath));
    }

    return $parseContent;
}

function getExitingKeys($struct): array
{
    return array_merge(array_keys(get_object_vars($struct)));
}

/**
 * @throws \Exception
 */
function genDiff(string $firstFilePath, string $secondFilePath): void
{
    $firstContent = getContent($firstFilePath);
    $secondContent = getContent($secondFilePath);

    printDiff($firstContent, $secondContent);
}

function printDiff($firstContent, $secondContent)
{
    $firstKeys = getExitingKeys($firstContent);
    $secondKeys = getExitingKeys($secondContent);

//    print_r("{\n");

    foreach (array_unique(array_merge($firstKeys, $secondKeys)) as $key) {
        $existsIn1 = in_array($key, $firstKeys);
        $existsIn2 = in_array($key, $secondKeys);

        if ($existsIn1 && $existsIn2) {
            $val1 = $firstContent->$key;
            $val2 = $secondContent->$key;

            if (compareObjects($val1, $val2)) {
                printObject($val1);
            } else {
                print_r(sprintf("  - %s: %s\n", $key, valueToString($val1)));
                print_r(sprintf("  + %s: %s\n", $key, valueToString($val2)));
            }
        } elseif ($existsIn1) {
            $val1 = $firstContent->$key;

            print_r(sprintf("  - %s: %s\n", $key, valueToString($val1)));
        } else {
            $val2 = $secondContent->$key;

            print_r(sprintf("  + %s: %s\n", $key, valueToString($val2)));
        }
    }

//    print_r("}\n");
}

function compareObjects($object1, $object2): bool
{
    if (!is_object($object1) || !is_object($object2)) {
        return $object1 === $object2;
    }

    $keys = getExitingKeys($object1);

    if (array_diff($keys, getExitingKeys($object2))) {
        return false;
    }

    foreach ($keys as $key) {
        $val1 = $object1->$key;
        $val2 = $object2->$key;

        if (!is_object($val1) || !is_object($val2)) {
            return $val1 === $val2;
        }

        return compareObjects($val1, $val2);
    }

    return true;
}

/**
 * @param mixed $value
 */
function valueToString($value): string
{
    switch (gettype($value)) {
        case 'string':
            return $value;
        case 'object':
            return objectToString($value);
        case 'NULL':
            return 'null';
        default:
            return var_export($value, true);
    }
}

function objectToString(object $object): string
{
    $result = "{\n";
    foreach (getExitingKeys($object) as $key) {
        $result .= '    '.$key.': '.valueToString($object->$key)."\n";
    }

    $result .= "}";

    return $result;
}

function printObject(object $object, int $depth = 0)
{
    print_r("{\n");

    foreach (getExitingKeys($object) as $key) {
        print_r(str_repeat(' ', $depth * 4 * 2).$key.':'.valueToString($object->$key)."\n");
    }

    print_r(str_repeat(' ', $depth * 4)."}\n");
}
