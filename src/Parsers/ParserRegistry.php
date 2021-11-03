<?php

declare(strict_types=1);

namespace DiffGenerator\Parsers\ParserRegistry;

use Closure;
use RuntimeException;

use function DiffGenerator\Parsers\JsonParser\parse as jsonParse;
use function DiffGenerator\Parsers\YamlParser\parse as yamlParse;

const INVALID_EXTENSION_MESSAGE = 'Extension "%s" is invalid.';

/**
 * @param string $extension
 *
 * @return Closure
 */
function getParserByFileExtension(string $extension): Closure
{
    switch ($extension) {
        case 'json':
            return function (string $content) {
                return jsonParse($content);
            };
        case 'yaml':
        case 'yml':
            return function (string $content) {
                return yamlParse($content);
            };
        default:
            throw new RuntimeException(sprintf(INVALID_EXTENSION_MESSAGE, $extension));
    }
}
