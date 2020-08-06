<?php 
declare(strict_types=1);

namespace PhpObfuscator;

class Shuffler
{
    /**
     * Função usada para desempacotar o código.
     *
     * @var string
     */
    private $packerFunction  = null;

    /**
     * Função usada para parametrizar o desempacotamento do código.
     *
     * @var string
     */
    private $argumentFunction = null;

    /**
     * Lista de funções embaralhadas com seus respectivos métodos de empacotamento/desempacotamento.
     * Os empacotadores/desempacotadores são fornecidos sem o sufixo.
     * Ex: 'packerOne' será invocado:
     * - como 'packerOnePack' para empacotar código ou
     * - como 'packerOneUnpack' para desempacotá-lo.
     *
     * @var array<string>
     */
    protected $packers = [];

    /**
     * Lista com as funções usadas para parametrizar o desempacotamento.
     *
     * @var array<string>
     */
    protected $arguments = [];

    public function __construct()
    {
        $this->shuffle();
    }

    private function shuffle(): void
    {
        $list = [
            'packerOne',
            'packerTwo',
            'packerThree',
        ];

        for ($x=0; $x<10; $x++) {

            // Funções desempacotadoras
            // 'func_96846372909684637290968463729023'  => 'packerOne',
            
            $packerName = "func_" . md5(uniqid("a" . $x . rand(), true));
            $this->packers[$packerName] = $list[array_rand($list)];

            // Funções argumentadoras
            // n => 'func_96846372909684637290968463729024',
            $argumentsName = "func_" . md5(uniqid("b" . $x . rand(), true));
            $this->arguments[] = $argumentsName;
        }
    }

    /**
     * Devolve uma lista com os nomes dos empacotadores
     * 
     * @return array<string>
     */
    public function mappedPackers(): array
    {
        return $this->packers;
    }

    /**
     * Devolve uma lista com os nomes dos argumentos
     * 
     * @return array<string>
     */
    public function mappedArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Devolve um nome randomicamente escolhido para o 'empacotador' que,
     * internamente, será responsável pela compressão/descompressão do código.
     *
     * @return string
     */
    public function packerName()
    {
        // Certifica que o nome não mude nesta instancia
        if ($this->packerFunction !== null) {
            return $this->packerFunction;
        }

        $listFunctions = array_keys($this->mappedPackers());
        $this->packerFunction = $listFunctions[array_rand($listFunctions)];
        return $this->packerFunction;
    }

    /**
     * Devolve um nome randomicamente escolhido para o método 'empacotador' que,
     * internamente, será responsável pela compressão/descompressão do código.
     * Este método será copiado para dentro de uma função que acompanhará o
     * código ofuscado para permitir a descompressão.
     *
     * @return string
     */
    public function packerMethodName()
    {
        $fakeName = $this->packerName();
        return $this->mappedPackers()[$fakeName];
    }

    /**
     * Devolve um nome randomicamente escolhido para a função
     * que será usada como argumento do desempacotador no ato
     * de desafazer a ofuscação.
     *
     * @return string
     */
    public function argumentName()
    {
        // Certifica que o nome não mude nesta instancia
        if ($this->argumentFunction  != null) {
            return $this->argumentFunction ;
        }

        $keys = $this->mappedArguments();
        $this->argumentFunction  = $keys[array_rand($keys)];
        return $this->argumentFunction ;
    }
}
