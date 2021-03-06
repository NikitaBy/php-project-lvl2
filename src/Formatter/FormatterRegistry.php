<?php

declare(strict_types=1);

namespace Differ\Formatter\FormatterRegistry;

use Closure;
use RuntimeException;

use function Differ\Formatter\Json\JsonFormatter\formatDiff as jsonFormatter;
use function Differ\Formatter\Plain\PlainFormatter\formatDiff as plainFormatter;
use function Differ\Formatter\Stylish\StylishFormatter\formatDiff as stylishFormatter;

const INVALID_FORMATTER_TYPE_MESSAGE = 'Formatter for type "%s" doesn\'t exits.';

/**
 * @param string $formatterType
 *
 * @return Closure
 */
function getFormatterByType(string $formatterType): Closure
{
    switch ($formatterType) {
        case 'stylish':
            return function (array $diff): string {
                return stylishFormatter($diff);
            };
        case 'plain':
            return function (array $diff): string {
                return plainFormatter($diff);
            };
        case 'json':
            return function (array $diff): string {
                return jsonFormatter($diff);
            };
        default:
            throw new RuntimeException(sprintf(INVALID_FORMATTER_TYPE_MESSAGE, $formatterType));
    }
}
