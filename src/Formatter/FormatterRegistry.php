<?php

declare(strict_types=1);

namespace DiffGenerator\Formatter\FormatterRegistry;

use Closure;
use RuntimeException;
use function DiffGenerator\Formatter\StylishFormatter\formatDiff;

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
                return formatDiff($diff);
            };
        default:
            throw new RuntimeException(sprintf(INVALID_FORMATTER_TYPE_MESSAGE, $formatterType));
    }
}
