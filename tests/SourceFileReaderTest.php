<?php

namespace MyQ\Test;

use MyQ\SourceFileReader;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \MyQ\SourceFileReader
 */
class SourceFileReaderTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::read
     * @covers ::__construct
     */
    public function it_reads_data_from_valid_file()
    {
        $reader = new SourceFileReader(__DIR__ . '/Fixtures/source.json');

        $expected = [
            'map' => [
                ['S', 'S', 'S', 'S'],
                ['S', 'S', 'C', 'S'],
                ['S', 'S', 'S', 'S'],
                ['S', null, 'S', 'S'],
            ],
            'start' => [
                'X' => 0,
                'Y' => 3,
                'facing' => 'N',
            ],
            'commands' => ['TL', 'A', 'C', 'A', 'C', 'TR', 'A', 'C'],
            'battery' => 80,
        ];

        $this->assertEquals($expected, $reader->read());
    }

    /**
     * @test
     *
     * @covers ::read
     * @covers ::__construct
     *
     * @expectedException \MyQ\Exceptions\FileException
     * @expectedExceptionMessage Invalid source file.
     */
    public function it_throws_file_exception_for_invalid_source_file()
    {
        $reader = new SourceFileReader(__DIR__ . '/Fixtures/invalid/file');

        $reader->read();
    }

    /**
     * @test
     *
     * @covers ::read
     * @covers ::__construct
     *
     * @expectedException \MyQ\Exceptions\FileException
     * @expectedExceptionMessage Invalid source json.
     */
    public function it_throws_file_exception_for_invalid_json_in_source_file()
    {
        $reader = new SourceFileReader(__DIR__ . '/Fixtures/invalid.json');

        $reader->read();
    }

    /**
     * @test
     *
     * @covers ::read
     * @covers ::validate
     *
     * @expectedException \MyQ\Exceptions\FileException
     * @expectedExceptionMessage Invalid source json.
     */
    public function it_throws_file_exception_for_invalid_data_in_json_file()
    {
        $reader = new SourceFileReader(__DIR__ . '/Fixtures/invalid-data.json');

        $reader->validate($reader->read(), ['map', 'start.X', 'start.Y', 'start.facing']);
    }

    /**
     * @test
     *
     * @covers ::validate
     */
    public function it_passes_validation_for_valid_json_file()
    {
        $reader = new SourceFileReader(__DIR__ . '/Fixtures/source.json');

        $this->assertNull(
            $reader->validate(
                $reader->read(),
                ['map', 'start.X', 'start.Y', 'start.facing', 'commands', 'battery']
            )
        );
    }
}
