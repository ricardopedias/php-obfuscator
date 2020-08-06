<?php

declare(strict_types=1);

namespace PhpObfuscator\Tests\Unit;

use PhpObfuscator\BlindMaker;
use PhpObfuscator\Tests\HasTestTools;
use PHPUnit\Framework\TestCase;

class BlindMakerTest extends TestCase
{
    use HasTestTools;

    /** @test */
    public function phpWrapperRemove()
    {
        array_walk($this->stubsList, function($stubName){

            $code = $this->filesystem()->read("Stubs/{$stubName}");
            $this->assertStringContainsString('<?php', $code);

            $blindy = new BlindMaker();

            $removed = $blindy->removePhpWrapper($code);
            $this->assertStringNotContainsString('<?php', $removed);
            $this->assertStringNotContainsString('?'.'>', $removed);
        });
    }

    /** @test */
    public function extractMethod()
    {
        $blindy = new BlindMaker();
        $content = $blindy->extractMethod('packerOnePack');
        $lines = explode("\n", $content);
        $lines = array_map(function($item){ return trim($item);}, $lines);
        
        $this->assertEquals([
            '{',
            '$encoded = base64_encode($data);',
            '',
            '// Separa em dois pedaços',
            '$partOne = mb_substr($encoded, 0, 10, "utf-8");',
            '$partTwo = mb_substr($encoded, 10, null, "utf-8");',
            '',
            '// Insere \'Sg\' para invalidar o base64',
            'return $partOne . $this->salt() . $partTwo;',
            '}'
        ], $lines);
    }

    //
    // Compressão e descompressão
    //

    /** @test */
    public function packingOneTime()
    {
        array_walk($this->stubsList, function($stubName){

            $blindy = new BlindMaker();

            $code = $this->filesystem()->read("Stubs/{$stubName}");
            $compressed = $blindy->packerOnePack($code);
            $this->assertEquals($code, $blindy->packerOneUnpack($compressed));
        });
    }

    /** @test */
    public function packingTwoTimes()
    {
        array_walk($this->stubsList, function($stubName){

            $blindy = new BlindMaker();

            $code = $this->filesystem()->read("Stubs/{$stubName}");
            $compressed = $blindy->packerTwoPack($code);
            $this->assertEquals($code, $blindy->packerTwoUnpack($compressed));
        });
    }

    /** @test */
    public function packingThreeTimes()
    {
        array_walk($this->stubsList, function($stubName){

            $blindy = new BlindMaker();

            $code = $this->filesystem()->read("Stubs/{$stubName}");
            $compressed = $blindy->packerThreePack($code);
            $this->assertEquals($code, $blindy->packerThreeUnpack($compressed));
        });
    }

}
