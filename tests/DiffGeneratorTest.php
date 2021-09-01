<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function DiffGenerator\DiffGenerator\genDiff;
use const DiffGenerator\DiffGenerator\INVALID_FILE_MESSAGE;
use const DiffGenerator\DiffGenerator\INVALID_PATH_MESSAGE;

/**
 * Class DiffGeneratorTest
 */
class DiffGeneratorTest extends TestCase
{
    public function testGenDiff(): void
    {
        $this->expectOutputString(file_get_contents(__DIR__.'/fixtures/result.txt'));
        genDiff(__DIR__.'/fixtures/file1.json', __DIR__.'/fixtures/file2.json');
    }

    public function testGenDiffInvalidJson(): void
    {
        $invalidFilePath = sprintf('%s/fixtures/invalidFile.json', __DIR__);

        $this->expectExceptionMessage(sprintf(INVALID_FILE_MESSAGE, $invalidFilePath));
        genDiff(__DIR__.'/fixtures/file1.json', $invalidFilePath);
    }

    public function testGenDiffInvalidPath():void
    {
        $invalidPath = sprintf('%s/fixtures/invalidPath.json', __DIR__);

        $this->expectExceptionMessage(sprintf(INVALID_PATH_MESSAGE, $invalidPath));
        genDiff(__DIR__.'/fixtures/file1.json', $invalidPath);
    }
}
