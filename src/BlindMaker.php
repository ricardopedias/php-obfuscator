<?php 
declare(strict_types=1);

namespace PhpObfuscator;

class BlindMaker
{
    private $saltString;

    public function __construct(?Shuffler $shuffler = null)
    {
        $this->shuffler = $shuffler ?? new Shuffler();
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
     * Remove os invólucros do PHP <?php e ?>
     * do código especificado
     *
     * @param string $code Código php sem ofuscar
     * @return string
     */
    public function removePhpWrapper(string $code): ?string
    {
        $matches = [];
        preg_match_all('/\<\?php|\<\?\=/i', $code, $matches);

        // Código misto não será ofuscado
        if(isset($matches[0]) && count($matches[0]) > 1) {
            return null;
        }

        return trim(str_replace(["<?php", "<?", "?>"], "", $code));
    }

    /**
     * Adiciona o invólucro do PHP <?php
     *
     * @param string $code Código php
     * @return string
     */
    public function addPhpWrapper(string $code): string
    {
        return "<?php " . $code;
    }

    /**
     * Transforma a string em código hexadecimal ASCII.
     *
     * @see http://php.net/manual/en/function.bin2hex.php
     * @param string $string
     * @return string
     */
    public function toASCII(string $string): string
    {
        $ascii = "";

        for ($i = 0; $i < strlen($string); $i ++) {
            $ascii .= '\x' . bin2hex($string[$i]);
        }

        return $ascii;
    }

    //
    // Os métodos abaixo são responsáveis pelo empacotamento do código
    //
    // PACK: Os métodos com sufixo 'Pack', por exemplo 'packerOnePack',
    // são responsáveis pelo empacotamento do código.
    //
    // UNPACK: O conteúdo dos métodos com sufixo 'Unpack', por exemplo 'packerOneUnpack',
    // são extraídos para gerar as funções responsáveis pelo
    // desempacotamento do código ofuscado.
    //

    /**
     * Empacota o codigo especificado.
     *
     * @param  string $data
     * @return string
     * @todo Mudar a sigla 'Sg' para que seja gerada dinamica e randomicamente
     */
    public function packerOnePack(string $data): string
    {
        $encoded = base64_encode($data);

        // Separa em dois pedaços
        $partOne = mb_substr($encoded, 0, 10, "utf-8");
        $partTwo = mb_substr($encoded, 10, null, "utf-8");

        // Insere 'Sg' para invalidar o base64
        return $partOne . $this->salt() . $partTwo;
    }

    /**
     * Remove 'Sg' para validar o base64
     *
     * @param  string  $data
     * @return string
     */
    public function packerOneUnpack(string $data): string
    {
        // Separa em dois pedaços
        $partOne = mb_substr($data, 0, 10, "utf-8");
        $partTwo = mb_substr($data, 12, null, "utf-8");
        return base64_decode($partOne . $partTwo);
    }

    /**
     * Empacota o codigo especificado.
     *
     * @param  string $data
     * @return string
     * @todo Mudar a sigla 'Sg' para que seja gerada dinamica e randomicamente
     */
    public function packerTwoPack(string $data): string
    {
        $encoded = base64_encode($data);

        // Separa em dois pedaços
        $partOne = mb_substr($encoded, 0, 5, "utf-8");
        $partTwo = mb_substr($encoded, 5, null, "utf-8");

        // Insere 'Sg' para invalidar o base64
        return $partOne . $this->salt() . $partTwo;
    }

    /**
     * Remove 'Sg' para validar o base64
     *
     * @param  string  $data
     * @return string
     */
    public function packerTwoUnpack(string $data): string
    {
        // Separa em dois pedaços
        $partOne = mb_substr($data, 0, 5, "utf-8");
        $partTwo = mb_substr($data, 7, null, "utf-8");
        return base64_decode($partOne . $partTwo);
    }

    /**
     * Empacota o codigo especificado.
     *
     * @param  string $data
     * @return string
     * @todo Mudar a sigla 'Sg' para que seja gerada dinamica e randomicamente
     */
    public function packerThreePack(string $data): string
    {
        $encoded = base64_encode($data);

        // Separa em dois pedaços
        $partOne = mb_substr($encoded, 0, 15, "utf-8");
        $partTwo = mb_substr($encoded, 15, null, "utf-8");

        // Insere 'Sg' para invalidar o base64
        return $partOne . 'Sg' . $partTwo;
    }

    /**
     * Remove 'Sg' para validar o base64
     *
     * @param  string  $data
     * @return string
     */
    public function packerThreeUnpack(string $data): string
    {
        // Separa em dois pedaços
        $partOne = mb_substr($data, 0, 15, "utf-8");
        $partTwo = mb_substr($data, 17, null, "utf-8");
        return base64_decode($partOne . $partTwo);
    }

    /**
     * Devolve o código php especificado de forma ofuscada.
     *
     * @param  string  $phpCode
     * @return string
     */
    public function obfuscateString(string $phpCode): ?string
    {
        $plainCode = $this->removePhpWrapper($phpCode);
        if ($plainCode === null) {
            return null;
        }

        return $this->wrapString($plainCode);
    }

    /**
     * Embrulha o código num container de ofuscação.
     *
     * @param  string $code
     * @return string
     */
    private function wrapString(string $code, bool $decodeErrors = false)
    {
        $prefix = $decodeErrors === false ? '' : '@';

        $packerMethod     = $this->shuffler()->packerMethodName();
        $unpackerFunction = $this->shuffler()->packerName();
        $argumentFunction = $this->shuffler()->argumentName();

        // Esconde o código com o desempacotador ramdômico
        $string = '';
        $string.= $this->toASCII($prefix. "eval({$unpackerFunction}("); // esconde a função de descompressão
        $string.= "'" . $this->{$packerMethod . "Pack"}($code) . "'";  // executa a função compactar
        $string.= $this->toASCII(",{$argumentFunction}()));");

        return "eval(\"{$string}\");";
    }

    /**
     * Embrulha o código num container de ofuscação.
     * Este método é usado apenas para empacotar as funções de desempacotamento.
     *
     * @param  string $code
     * @return string
     */
    public function wrapUnpackFunctions(string $code, bool $decodeErrors = false)
    {
        $prefix = $decodeErrors === false ? '' : '@';

        // A função php_zencodign é para ofuscar as 'funções de descompressão'
        // usadas para desafazer a ofuscação de todos os arquivos php
        $phpZencoding  = "if(function_exists('php_zencoding') == false){\n";
        $phpZencoding .= "function php_zencoding(\$data)\n" . $this->extractMethod('packerOneUnpack');
        $phpZencoding .= "\n}";

        // Esconde a função de desempacotamento no próprio arquivo
        $zen = '';
        $zen .= $this->toASCII($prefix . "eval(base64_decode("); // esconde a função de descompressão
        $zen .= "'" . base64_encode($phpZencoding) . "'";  // executa a função compactar
        $zen .= $this->toASCII("));");

        // Esconde o código com o desempacotador php_zencoding
        $string = '';
        $string.= $this->toASCII($prefix . "eval(php_zencoding("); // esconde a função de descompressão
        $string.= "'" . $this->packerOnePack($code) . "'";         // comprime o código
        $string.= $this->toASCII("));");

        return "eval(\"{$zen}\"); eval(\"{$string}\");";
    }

    /**
     * Extrai o conteúdo de um método público de desenpacotamento 'packer???Unpack',
     * existente na classe BlindMaker. O conteúdo do método será usado para gerar as funções
     * de desempacotamento.
     * Veja como isso é feito no método getRevertFileContents
     *
     * @param  string $methodName
     * @return string
     */
    public function extractMethod($methodName)
    {
        $method = new \ReflectionMethod(__CLASS__, $methodName);
        $start_line = $method->getStartLine(); // it's actually - 1, otherwise you wont get the function() block
        $end_line = $method->getEndLine();
        $length = $end_line - $start_line;

        $source = file(__FILE__);
        return implode("", array_slice($source, $start_line, $length));
    }

    /**
     * Gera uma string contendo todas as funções de desempacotamento.
     *
     * @param  boolean $use_php_wrapper
     * @return string
     */
    public function getRevertFileContents()
    {
        $lines = [];

        $sp = "    ";

        // DESEMPACOTADORES RANDÔMICOS:
        // São várias funções 'falsas' com nomes ramdômicos que,
        // internamente, invocam as funções reais de desempacotamento.
        // Apenas para dificultar o entendimento do hacker :)
        //
        // As chaves e valores são no formato 'desempacotador => metodo'.
        // Ex.:
        // 'cfForgetShow'  => 'packerOne',
        // 'cryptOf'       => 'packerTwo',
        $mappedPackers = $this->shuffler()->mappedPackers();
        foreach($mappedPackers as $packerName => $methodName) {
            // Renomeia o prefixo do método 'packerOne'
            // para nomear na função de desempacotamento como 'baseOne'
            $baseName = str_replace('packer', 'base', $methodName);

            $lines[] = "if (function_exists('{$packerName}') == false){\n"
                     . $sp . "function {$packerName}(\$data, \$revert = false){\n"
                     . $sp .$sp . "return {$baseName}(\$data);\n"
                     . $sp . "}\n"
                     . "}";
        }

        // DESEMPACOTADORES REAIS:
        // Os desempacotadores randômicos invocam três 'efetivos':
        // baseOne, baseTwo e baseThree
        $bases = array_unique($mappedPackers);
        foreach($bases as $methodName) {
            // Renomeia o prefixo do método 'packerOne'
            // para nomear na função de desempacotamento como 'baseOne'
            $baseName = str_replace('packer', 'base', $methodName);
            $lines[] = "if (function_exists('{$baseName}') == false){\n"
                     . $sp . "function {$baseName}(\$data)\n"
                     // Extrai o conteúdo do método 'packer???Unpack'
                     // para gerar a função 'base???'
                     . $this->extractMethod($methodName . 'Unpack')
                     . "}";
        }

        // ARGUMENTADORES RAMDÔMICOS:
        // São várias funções 'falsas' com nomes ramdômicos que,
        // internamente apenas devolvem uma valor booleano e que
        // serão usadas como argumentos da função desempacotadora
        //
        // Veja como isso é feito no método wrapString
        $mappedArgumenters = $this->shuffler()->mappedArguments();
        foreach($mappedArgumenters as $methodName) {
            $lines[] = "if (function_exists('{$methodName}') === false){\n"
                     . $sp . "function {$methodName}() { return TRUE; }\n"
                     . "}";
        }

        return implode("\n", $lines);
    }

    /**
     * Verifica se o arquivo especificado já está ofuscado.
     *
     * @param  string $obfuscatedFile
     * @return true
     */
    public function isObfuscatedFile(string $obfuscatedFile) : bool
    {
        $directory = dirname($obfuscatedFile);
        $fs = Filesystem::instance($directory);

        $contents = $fs->read($fs->basename($obfuscatedFile));
        if ($contents === false) {
            // não foi possível ler o arquivo
            return false;
        }

        if (empty(preg_match('#<\?php#', $contents)) == false
         && empty(preg_match('#x28#', $contents)) == false
         && empty(preg_match('#x29#', $contents)) == false
         && empty(preg_match('#eval#', $contents)) == false
        ) {
            return true;
        }

        return false;
    }

    public function removeWhiteSpaces(string $string): string
    {
        return php_strip_whitespace($string);
    }

    private function salt(): string
    {
        if ($this->saltString === null) {
            $this->saltString = 'Sg'; // Deve conter duas letras!!
        }
        
        return $this->saltString;
    }
}
