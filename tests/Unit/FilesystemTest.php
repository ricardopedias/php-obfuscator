<?php

declare(strict_types=1);

namespace PhpObfuscator\Tests\Unit;

use PhpObfuscator\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    /** @test */
    public function extends()
    {
        $fs = Filesystem::instance(__DIR__ . '/../');
        $this->assertIsArray($fs->listContents());
    }
}
