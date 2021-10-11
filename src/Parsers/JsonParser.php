<?php

namespace DiffGenerator\Parsers\JsonParser;

/**
 * @param string $content
 *
 * @return mixed[]|null
 */
function parse(string $content)
{
    return json_decode($content);
}
