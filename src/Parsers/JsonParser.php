<?php

namespace DiffGenerator\Parsers\JsonParser;

function parse(string $content): ?array
{
    return json_decode($content, true);
}
