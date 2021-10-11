<?php

declare(strict_types=1);

namespace DiffGenerator\Parsers\YamlParser;

use Symfony\Component\Yaml\Yaml;

function parse(string $content)
{
    try {
        return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
    } catch (\Exception $e) {
        return null;
    }
}
