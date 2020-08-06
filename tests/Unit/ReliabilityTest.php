<?php

declare(strict_types=1);

namespace PhpObfuscator\Tests\Unit;

use PhpObfuscator\Reliability;
use PHPUnit\Framework\TestCase;

class ReliabilityTest extends TestCase
{
    /** @test */
    public function encodeBase64()
    {
        $object = new Reliability();

        $string = 'teste de codificação';
        $encoded = base64_encode($string);

        $this->assertEquals($encoded, $object->encodeBase64($string));
    }

    /** @test */
    public function decodeBase64()
    {
        $object = new Reliability();

        $string = 'teste de codificação';
        $encoded = base64_encode($string);

        $this->assertEquals($string, $object->decodeBase64($encoded));
    }

    public function pathProvider()
    {
        return [
            ['/var/tmp/base/teste', 'teste'],
            ['/var/tmp/base/teste.txt', 'teste.txt'],
            ['/var/tmp/base/teste...txt', 'teste...txt'],
        ];
    }

    /** 
     * @test 
     * @dataProvider pathProvider
     */
    public function basename($input, $expected)
    {
        $object = new Reliability();
        $this->assertEquals($expected, $object->basename($input));
    }

    public function filenameProvider()
    {
        return [
            ['/var/tmp/base/teste', 'teste'],
            ['/var/tmp/base/teste.txt', 'teste'],
            ['/var/tmp/base/teste...txt', 'teste..'],
        ];
    }

    /** 
     * @test 
     * @dataProvider filenameProvider
     */
    public function filename($input, $expected)
    {
        $object = new Reliability();
        $this->assertEquals($expected, $object->filename($input));
    }

    public function dirnameProvider()
    {
        return [
            ['/var/tmp/base/teste', '/var/tmp/base'],
            ['/../../base/teste.txt', '/../../base'],
            ['../../base/teste.txt', '../../base'],
        ];
    }

    /** 
     * @test 
     * @dataProvider dirnameProvider
     */
    public function dirname($input, $expected)
    {
        $object = new Reliability();
        $this->assertEquals($expected, $object->dirname($input));
    }

    public function isDirectoryProvider()
    {
        return [
            [__DIR__, true],
            [__DIR__ . '/bla/bla', false],
            [__DIR__ . '/ReliabilityTest.php', false],
        ];
    }

    /** 
     * @test 
     * @dataProvider isDirectoryProvider
     */
    public function isDirectory($input, $expected)
    {
        $object = new Reliability();
        $this->assertSame($expected, $object->isDirectory($input));
    }

    public function isFileProvider()
    {
        return [
            [__DIR__, false],
            [__DIR__ . '/bla/bla', false],
            [__DIR__ . '/ReliabilityTest.php', true],
            [__DIR__ . '/BlablaTest.php', false],
        ];
    }

    /** 
     * @test
     * @dataProvider isFileProvider
     */
    public function isFile($input, $expected)
    {
        $object = new Reliability();
        $this->assertSame($expected, $object->isFile($input));
    }

    /** @test */
    public function readFileLines()
    {
        $object = new Reliability();

        $file = $object->readFileLines(__DIR__ . '/ReliabilityTest.php');
        $this->assertIsArray($file);
        $this->assertEquals('<?php', $file[0]);
        $this->assertEquals('declare(strict_types=1);', $file[2]);
        $this->assertEquals('namespace PhpObfuscator\Tests\Unit;', $file[4]);
    }

    /** @test */
    public function readFileLinesOfEmptyFile()
    {
        $object = new Reliability();

        $lines = $object->readFileLines(__DIR__ . '/../Stubs/Empty.stub');
        $this->assertIsArray($lines);
        $this->assertCount(0, $lines);
    }
}
