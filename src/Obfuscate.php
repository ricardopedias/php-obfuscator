<?php 
declare(strict_types=1);

namespace PhpObfuscator;

use InvalidArgumentException;
use PhpObfuscator\Contracts\IFeedback;

class Obfuscate
{
    /**
     * Controla se o código, depois de ofuscado, pode disparar erros para o
     * usuário ou se eles devem ocorrer silenciosamente sem serem reportados
     *
     * @var bool
     */
    private $decodeErrors = true;

    /**
     * O código resultante do processo de ofuscação
     * é armazenado neste atributo.
     *
     * @var string
     */
    private $obfuscated    = '';

    /**
     * Objeto contendo informações de feedback sobre o que acontece
     * dentro do código ofuscado.
     *
     * @var \PhpObfuscator\Contracts\IFeedback
     */
    private $feedback;

    /**
     * Objeto contendo informações de feedback sobre o que acontece
     * dentro do código ofuscado.
     *
     * @var \PhpObfuscator\Shuffler
     */
    private $shuffler;

    /**
     * Objeto contendo as funcionalidades de ofuscação.
     *
     * @var \PhpObfuscator\BlindMaker
     */
    private $blindMaker;

    public function __construct(?IFeedback $feedback = null)
    {
        $this->feedback   = $feedback ?? new Feedback();
        $this->shuffler   = new Shuffler();
        $this->blindMaker = new BlindMaker($this->shuffler);
    }

    /**
     * Obtém o objeto de feedbacks.
     *
     * @return \PhpObfuscator\Contracts\IFeedback
     */
    public function feedback(): IFeedback
    {
        return $this->feedback;
    }

    /**
     * Obtém o objeto de remdomização.
     *
     * @return \PhpObfuscator\Shuffler
     */
    public function shuffler(): Shuffler
    {
        return $this->shuffler;
    }

    /**
     * Obtém o objeto de remdomização.
     *
     * @return \PhpObfuscator\BlindMaker
     */
    public function blindMaker(): BlindMaker
    {
        return $this->blindMaker;
    }

    /**
     * Controla se o código, depois de ofuscado, pode disparar erros para o
     * usuário ou se eles devem ocorrer silenciosamente sem ser reportados
     *
     * @param  boolean $enable
     * @return \PhpObfuscator\Obfuscate
     */
    public function enableDecodeErrors($enable = true) : Obfuscate
    {
        $this->decodeErrors = $enable;
        return $this;
    }

    /**
     * Controla se o código, depois de ofuscado, pode disparar erros para o
     * usuário ou se eles devem ocorrer silenciosamente sem ser reportados
     *
     * @param  boolean $enable
     * @return \PhpObfuscator\Obfuscate
     */
    public function enableThrowErrors($enable = true) : Obfuscate
    {
        $this->feedback()->enableThrowErrors($enable);
        return $this;
    }

    /**
     * Verifica se o arquivo especificado já está ofuscado.
     *
     * @param  string $file Caminho completo até o arquivo
     * @return true
     */
    public function isObfuscatedFile(string $file) : bool
    {
        return $this->blindMaker()->isObfuscatedFile($file);
    }

    /**
     * Devolve o código resultante do processo de ofuscação.
     * Se a ofuscação ocorrer com sucesso, uma string ofuscada será devolvida,
     * caso contrário, a string original será retornada no lugar.
     *
     * @return string
     */
    public function getObfuscated()
    {
        return $this->obfuscated;
    }

    /**
     * Ofusca o arquivo especificado e armazena-o na memória.
     *
     * @param  string $file
     * @return bool
     */
    public function from(string $file) : Obfuscate
    {
        if (substr($file, -3) !== 'php') {
            $this->feedback()->addErrorMessage("Only PHP files can be obfuscated!");
            throw new InvalidArgumentException("Only PHP files can be obfuscated!");
        }

        // Remove os espaços e comentários do arquivo
        $contents = $this->blindMaker()->removeWhiteSpaces($file);

        $this->obfuscated = $this->blindMaker()->obfuscateString($contents, $this->decodeErrors);

        if ($this->obfuscated === null) {
            $this->feedback()->addRuntimeMessage("Mixed code found. File not obfuscated!");
            $this->obfuscated = $contents;
        }

        return $this;
    }

    /**
     * Salva um arquivo com o código ofuscado no caminho especificado.
     *
     * @param  string $pathDestiny
     * @param bool $self_contained
     * @return bool
     */
    public function generate(string $pathDestiny) : bool
    {
        $revertFunctions = $this->blindMaker()->getRevertFileContents();

        $contents = $this->blindMaker()->addPhpWrapper(''
            . $this->obfuscateUnpackFunctions($revertFunctions)
            . $this->getObfuscated()
        );

        $directory = Filesystem::dirname($pathDestiny);
        $file = Filesystem::basename($pathDestiny);

        $result = Filesystem::instance($directory)->write($file, $contents);

        return $result !== false;
    }

    /**
     * Devolve o código php especificado de forma ofuscada.
     * A string especificada deve conter apenas as funções de desempacotamento,
     * pois a forma de ofuscação é diferente para elas poderem funcionar.
     *
     * @param  string  $unpackCode
     * @return string
     */
    private function obfuscateUnpackFunctions(string $unpackCode): string
    {
        $plainCode = $this->blindMaker()->removePhpWrapper($unpackCode);
        if ($plainCode === null) {
            return false;
        }

        return $this->blindMaker()->wrapUnpackFunctions($plainCode, $this->decodeErrors);
    }
}
