<?php

declare(strict_types=1);

namespace PhpObfuscator\Tests\Unit;

use PhpObfuscator\Shuffler;
use PHPUnit\Framework\TestCase;

class ShufflerTest extends TestCase
{
    /** @test */
    public function contructor()
    {
        $object = new Shuffler();
        $this->assertIsArray($object->mappedPackers());
        $this->assertCount(10, $object->mappedPackers());

        $this->assertIsArray($object->mappedArguments());
        $this->assertCount(10, $object->mappedArguments());
    }

    /** @test */
    public function packerName()
    {
        $object = new Shuffler();
        $nameOne = $object->packerName();
        $nameTwo = $object->packerName();
        $this->assertEquals($nameOne, $nameTwo);
    }

    /** @test */
    public function getPackerMethodName()
    {
        $object = new Shuffler();

        $nameOne = $object->packerMethodName();
        $nameTwo = $object->packerMethodName();
        $this->assertEquals($nameOne, $nameTwo);
    }

    /** @test */
    public function getArgumenterName()
    {
        $object = new Shuffler();

        $nameOne = $object->argumentName();
        $nameTwo = $object->argumentName();
        $this->assertEquals($nameOne, $nameTwo);
    }

    /** @test */
    public function repeat()
    {
        for($x=0; $x<50; $x++) {

            $object = new Shuffler();
            
            $packerOne = $object->packerMethodName();
            $packerTwo = $object->packerMethodName();
            $this->assertEquals($packerOne, $packerTwo);

            $argumentOne = $object->argumentName();
            $argumentTwo = $object->argumentName();
            $this->assertEquals($argumentOne, $argumentTwo);

        }
        
    }

}
