<?php

declare(strict_types=1);

namespace Differ\Test;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

use const Differ\Differ\INVALID_FILE_MESSAGE;
use const Differ\Differ\INVALID_PATH_MESSAGE;
use const Differ\Formatter\FormatterRegistry\INVALID_FORMATTER_TYPE_MESSAGE;
use const Differ\Parsers\ParserRegistry\INVALID_EXTENSION_MESSAGE;

/**
 * Class DiffGeneratorTest
 */
class DifferTest extends TestCase
{
    /**
     * @return string[][]
     */
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

    /**
     * @return string[][]
     */
    public function dataProviderValid(): array
    {
        return [
            [
                sprintf('%s/fixtures/json/file1.json', __DIR__),
                sprintf('%s/fixtures/json/file2.json', __DIR__),
                'stylish',
            ],
            [
                sprintf('%s/fixtures/yaml/file1.yaml', __DIR__),
                sprintf('%s/fixtures/yaml/file2.yml', __DIR__),
                'stylish',
            ],
            [
                sprintf('%s/fixtures/json/file1.json', __DIR__),
                sprintf('%s/fixtures/json/file2.json', __DIR__),
                'plain',
            ],
            [
                sprintf('%s/fixtures/yaml/file1.yaml', __DIR__),
                sprintf('%s/fixtures/yaml/file2.yml', __DIR__),
                'plain',
            ],
            [
                sprintf('%s/fixtures/json/file1.json', __DIR__),
                sprintf('%s/fixtures/json/file2.json', __DIR__),
                'json',
            ],
            [
                sprintf('%s/fixtures/yaml/file1.yaml', __DIR__),
                sprintf('%s/fixtures/yaml/file2.yml', __DIR__),
                'json',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderValid
     *
     * @param string $firstFilePath
     * @param string $secondFilePath
     * @param string $formatterType
     */
    public function testGenDiff(string $firstFilePath, string $secondFilePath, string $formatterType): void
    {
        /** @var string $expectedString */
        $expectedString = file_get_contents(sprintf('%s/fixtures/results/result_%s.txt', __DIR__, $formatterType));
        $this->expectOutputString($expectedString);

        genDiff($firstFilePath, $secondFilePath, $formatterType);
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testGenDiffInvalidFormatterType(): void
    {
        $invalidFormatterType = 'fake';

        $this->expectExceptionMessage(sprintf(INVALID_FORMATTER_TYPE_MESSAGE, $invalidFormatterType));

        $filePath = sprintf('%s/fixtures/json/file1.json', __DIR__);
        genDiff($filePath, $filePath, $invalidFormatterType);
    }

    public function testGenDiffInvalidPath(): void
    {
        $invalidPath = sprintf('%s/fixtures/invalidPath.json', __DIR__);

        $this->expectExceptionMessage(sprintf(INVALID_PATH_MESSAGE, $invalidPath));

        genDiff($invalidPath, $invalidPath);
    }
}
