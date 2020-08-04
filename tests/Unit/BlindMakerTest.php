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
