<?php

declare(strict_types=1);

namespace PhpObfuscator\Tests;

use PhpObfuscator\Obfuscate;
use Mockery;
use League\Flysystem\Adapter;
use League\Flysystem\Filesystem;

trait HasTestTools
{
    private $stubsList = [
        'PhpClass.stub',
        'PhpClassClosed.stub',
        'PhpClassNamespaced.stub',
        'PhpProcedural.stub',
        'PhpProceduralClosed.stub'
    ];

    protected function setUp():void
    {
        $this->clearAll();
        $this->prepareStubs();
    }

    protected function tearDown(): void
    {
        $this->clearAll();
    }

    protected function filesystem()
    {
        $adapter = new Adapter\Local(__DIR__);
        return new Filesystem($adapter);    
    }

    private function clearAll(): void
    {
        if ($this->filesystem()->has('Runtime') === true) {
            $this->filesystem()->deleteDir('/Runtime');
        }
    }

    protected function prepareStubs(): void
    {
        $this->filesystem()->createDir('Runtime');
    }

    protected function getMocked()
    {
        $mockery = new Mockery\Container();

        $obfuscate = $mockery->mock(Obfuscate::class)->makePartial();
        $obfuscate->shouldAllowMockingProtectedMethods();
        return $obfuscate;
    }
}
