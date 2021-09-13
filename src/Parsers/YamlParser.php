<?php

declare(strict_types=1);

namespace DiffGenerator\Parsers\YamlParser;

use Symfony\Component\Yaml\Yaml;

/**
 * @param string $content
 *
 * @return mixed[]/null
 */
function parse(string $content): ?array
{
    try {
        return (array) Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
    } catch (\Exception $e) {
        return null;
    }
}
