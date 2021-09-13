<?php

declare(strict_types=1);

namespace DiffGenerator\Test;

use PHPUnit\Framework\TestCase;
use function DiffGenerator\genDiff;
use const DiffGenerator\INVALID_EXTENSION_MESSAGE;
use const DiffGenerator\INVALID_FILE_MESSAGE;
use const DiffGenerator\INVALID_PATH_MESSAGE;

/**
 * Class DiffGeneratorTest
 */
class DiffGeneratorTest extends TestCase
{
    public function dataProviderInvalid(): array
    {
        return [
            [
                __DIR__.'/fixtures/json/file1.json',
                __DIR__.'/fixtures/json/invalidFile.json',
            ],
            [
                __DIR__.'/fixtures/yaml/file1.yaml',
                __DIR__.'/fixtures/yaml/invalidFile.yaml',
            ],
        ];
    }

    public function dataProviderValid(): array
    {
        return [
            [
                __DIR__.'/fixtures/json/file1.json',
                __DIR__.'/fixtures/json/file2.json',
            ],
            [
                __DIR__.'/fixtures/yaml/file1.yaml',
                __DIR__.'/fixtures/yaml/file2.yml',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderValid
     *
     * @param string $firstFilePath
     * @param string $secondFilePath
     */
    public function testGenDiff(string $firstFilePath, string $secondFilePath): void
    {
        /** @var string $expectedString */
        $expectedString = file_get_contents(__DIR__.'/fixtures/result.txt');
        $this->expectOutputString($expectedString);

        genDiff($firstFilePath, $secondFilePath);
    }

    public function testGenDiffInvalidExtension(): void
    {
        $invalidExtensionFilePath = sprintf('%s/fixtures/invalidExtension.invld', __DIR__);

        $this->expectExceptionMessage(
            sprintf(INVALID_EXTENSION_MESSAGE, pathinfo($invalidExtensionFilePath, PATHINFO_EXTENSION))
        );

        genDiff($invalidExtensionFilePath, $invalidExtensionFilePath);
    }

    /**
     * @dataProvider dataProviderInvalid
     *
     * @param string $firstFilePath
     * @param string $invalidFilePath
     */
    public function testGenDiffInvalidFile(string $firstFilePath, string $invalidFilePath): void
    {
        $this->expectExceptionMessage(sprintf(INVALID_FILE_MESSAGE, $invalidFilePath));

        genDiff($firstFilePath, $invalidFilePath);
    }

    public function testGenDiffInvalidPath(): void
    {
        $invalidPath = sprintf('%s/fixtures/invalidPath.json', __DIR__);

        $this->expectExceptionMessage(sprintf(INVALID_PATH_MESSAGE, $invalidPath));

        genDiff($invalidPath, $invalidPath);
    }
}
