<?php

declare(strict_types=1);

namespace DiffGenerator\Parsers\YamlParser;

use Symfony\Component\Yaml\Yaml;

function parse(string $content): ?array
{
    try {
        return (array) Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
    } catch (\Exception $e) {
        return null;
    }
}
