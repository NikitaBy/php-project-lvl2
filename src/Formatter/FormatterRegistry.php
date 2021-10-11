<?php

declare(strict_types=1);

namespace DiffGenerator\Formatter\FormatterRegistry;

use Closure;
use RuntimeException;
use function DiffGenerator\Formatter\StylishFormatter\format as stylishFormat;

const INVALID_FORMATTER_TYPE_MESSAGE = 'Formatter for type "%s" doesn\'t exits.';

function getFormatterByType(string $formatterType): Closure
{
    switch ($formatterType) {
        case 'stylish':
            return function ($raw): string {
                return stylishFormat($raw);
            };
        default:
            throw new RuntimeException(sprintf(INVALID_FORMATTER_TYPE_MESSAGE, $formatterType));
    }
}
