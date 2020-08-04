<?php

declare(strict_types=1);

namespace PhpObfuscator\Tests\Unit;

use InvalidArgumentException;
use PhpObfuscator\Feedback;
use PhpObfuscator\Obfuscate;
use PhpObfuscator\Tests\HasTestTools;
use PHPUnit\Framework\TestCase;

class ObfuscateTest extends TestCase
{
    use HasTestTools;

    /** @test */
    public function contructor()
    {
        // default
        $obfuscate = new Obfuscate();
        $this->assertInstanceOf(Feedback::class, $obfuscate->feedback());
        $this->assertEquals('', $obfuscate->feedback()->getLastErrorMessage());

        $feedback = new Feedback();   
        $feedback->addErrorMessage('teste');
        $obfuscate = new Obfuscate($feedback);
        $this->assertInstanceOf(Feedback::class, $obfuscate->feedback());
        $this->assertEquals('teste', $obfuscate->feedback()->getLastErrorMessage());

    }

    //
    // Funções aleatórias
    //

    /** @test */
    public function isObfuscatedFile()
    {
        $notObfuscated = __DIR__ . '/../Stubs/PhpClass.stub';
        $obfuscated = __DIR__ . '/../Stubs/PhpClassObfuscated.stub';

        $this->assertFalse((new Obfuscate())->isObfuscatedFile($notObfuscated));
        $this->assertTrue((new Obfuscate())->isObfuscatedFile($obfuscated));
    }

    //
    // Ofuscação e Execução
    //

    /** @test */
    public function obfuscateFileError()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only PHP files can be obfuscated!');

        $ob = new Obfuscate();
        $this->assertFalse($ob->from('arquivo-nao-php.html'));
    }

    /** @test */
    public function obfuscatePhpProceduralMixed()
    {
        $content = $this->filesystem()->read('Stubs/PhpProceduralMixed.stub');
        $this->filesystem()->write('Runtime/PhpProceduralMixed.php', $content);

        $phpFile = __DIR__ . '/../Runtime/PhpProceduralMixed.php';

        // Ofusca o arquivo e salva do disco
        $ob = new Obfuscate();
        $ob->from($phpFile);

        $this->assertEquals('Mixed code found. File not obfuscated!', $ob->feedback()->getLastRuntimeMessage());
    }

    /** @test */
    public function obfuscatePhpClass()
    {
        $content = $this->filesystem()->read('Stubs/PhpClass.stub');
        $this->filesystem()->write('Runtime/PhpClass.php', $content);

        $phpFile = __DIR__ . '/../Runtime/PhpClass.php';
        $obfFile = __DIR__ . '/../Runtime/PhpTest.php';

        // Ofusca o arquivo e salva do disco
        $ob = new Obfuscate();
        $ob->enableDecodeErrors();
        $this->assertTrue($ob->from($phpFile)->generate($obfFile));

        // Inclusão do arquivo ofuscado
        include_once $obfFile;

        // executa a classe ofuscada
        $className = '\PhpClass';
        $this->assertEquals((new $className())->method(), 'PhpClass executando com sucesso');
    }

    public function testObfuscatePhpClassClosed()
    {
        $content = $this->filesystem()->read('Stubs/PhpClassClosed.stub');
        $this->filesystem()->write('Runtime/PhpClassClosed.php', $content);

        $phpFile = __DIR__ . '/../Runtime/PhpClassClosed.php';
        $obfFile = __DIR__ . '/../Runtime/PhpTestClosed.php';

        // Ofusca o arquivo e salva do disco
        $ob = new Obfuscate();
        $ob->enableDecodeErrors();
        $this->assertTrue($ob->from($phpFile)->generate($obfFile));

        // Inclusão do arquivo ofuscado
        include_once $obfFile;

        // executa a classe ofuscada
        $className = '\PhpClassClosed';
        $this->assertEquals((new $className())->method(), 'PhpClassClosed executando com sucesso');
    }

    public function testObfuscatePhpClassNamespaced()
    {
        $content = $this->filesystem()->read('Stubs/PhpClassNamespaced.stub');
        $this->filesystem()->write('Runtime/PhpClassNamespaced.php', $content);

        $phpFile = __DIR__ . '/../Runtime/PhpClassNamespaced.php';
        $obfFile = __DIR__ . '/../Runtime/PhpTestNamespaced.php';

        // Ofusca o arquivo e salva do disco
        $ob = new Obfuscate();
        $ob->enableDecodeErrors();
        $this->assertTrue($ob->from($phpFile)->generate($obfFile));

        // Inclusão do arquivo ofuscado
        include_once $obfFile;

        // executa a classe ofuscada
        $className = '\Php\Name\Space\PhpClassNamespaced';
        $this->assertEquals((new $className())->method(), 'Php\Name\Space\PhpClassNamespaced executando com sucesso');
    }

    public function testObfuscatePhpProcedural()
    {
        $content = $this->filesystem()->read('Stubs/PhpProcedural.stub');
        $this->filesystem()->write('Runtime/PhpProcedural.php', $content);

        $phpFile = __DIR__ . '/../Runtime/PhpProcedural.php';
        $obfFile = __DIR__ . '/../Runtime/PhpTestProcedural.php';

        // Ofusca o arquivo e salva do disco
        $ob = new Obfuscate();
        $ob->enableDecodeErrors();
        $this->assertTrue($ob->from($phpFile)->generate($obfFile));

        // Inclusão do arquivo ofuscado
        include_once $obfFile;

        // executa a função ofuscada
        $functionName = '\PhpProcedural';
        $this->assertEquals($functionName(), 'PhpProcedural executando com sucesso');
    }

    public function testObfuscatePhpProceduralClosed()
    {
        $content = $this->filesystem()->read('Stubs/PhpProceduralClosed.stub');
        $this->filesystem()->write('Runtime/PhpProceduralClosed.php', $content);

        $phpFile = __DIR__ . '/../Runtime/PhpProceduralClosed.php';
        $obfFile = __DIR__ . '/../Runtime/PhpTestProceduralClosed.php';

        // Ofusca o arquivo e salva do disco
        $ob = new Obfuscate();
        $ob->enableDecodeErrors();
        $this->assertTrue($ob->from($phpFile)->generate($obfFile));

        // Inclusão do arquivo ofuscado
        include_once $obfFile;

        // executa a função ofuscada
        $functionName = '\PhpProceduralClosed';
        $this->assertEquals($functionName(), 'PhpProceduralClosed executando com sucesso');
    }
}
