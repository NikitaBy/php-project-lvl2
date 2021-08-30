<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function DiffGenerator\DiffGenerator\genDiff;

/**
 * Class DiffGeneratorTest
 */
class DiffGeneratorTest extends TestCase
{
    private $expectedOutput = "{\n  - follow: false\n    host: hexlet.io\n  - proxy: 123.234.53.22\n  - timeout: 50\n  + timeout: 20\n  + verbose: true\n}\n";

    public function testGenDiff()
    {
        $this->expectOutputString($this->expectedOutput);
        genDiff('fixtures/file1.json', 'fixtures/file2.json');
    }

    public function testGenDiffInvalidJson()
    {
        $this->expectException(InvalidArgumentException::class);
        genDiff('fixtures/file1.json', 'fixtures/invalidFilde.json');
    }
}
