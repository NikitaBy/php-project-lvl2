<?php

namespace DiffGenerator\Parsers\JsonParser;

/**
 * @param string $content
 *
 * @return mixed[]|null
 */
function parse(string $content): ?array
{
    return json_decode($content, true);
}
