<?php

namespace Differ\Differ;

use Exception;

use function Differ\Formatter\FormatterRegistry\getFormatterByType;
use function Differ\Parsers\ParserRegistry\getParserByFileExtension;
use function DiffHelper\calculateDiff;

const INVALID_FILE_MESSAGE = '"%s" is invalid.';
const INVALID_PATH_MESSAGE = '"%s" doesn\'t exists.';

/**
 * @param string $path
 *
 * @return string|null
 */
function actualizePath(string $path): ?string
{
    if (file_exists($path)) {
        return $path;
    }

    $path = sprintf('%s/../%s', __DIR__, $path);

    return file_exists($path) ? $path : null;
}

/**
 * @param string $path
 *
 * @return mixed
 *
 * @throws Exception
 */
function getContent(string $path)
{
    if (!$actualPath = actualizePath($path)) {
        throw new Exception(sprintf(INVALID_PATH_MESSAGE, $path));
    }

    if (!$content = file_get_contents($actualPath)) {
        throw new Exception(sprintf(INVALID_FILE_MESSAGE, $actualPath));
    }

    $parser = getParserByFileExtension(pathinfo($actualPath, PATHINFO_EXTENSION));

    if (!$parseContent = $parser($content)) {
        throw new Exception(sprintf(INVALID_FILE_MESSAGE, $actualPath));
    }

    return $parseContent;
}

/**
 * @param string $firstFilePath
 * @param string $secondFilePath
 * @param string $formatterType
 *
 * @return string
 */
function genDiff(string $firstFilePath, string $secondFilePath, string $formatterType = 'stylish'): string
{
    $firstContent = getContent($firstFilePath);
    $secondContent = getContent($secondFilePath);

    $diff = calculateDiff($firstContent, $secondContent);

    $formatter = getFormatterByType($formatterType);

    return $formatter($diff);
}
