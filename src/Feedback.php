<?php 
declare(strict_types=1);

namespace PhpObfuscator;

use PhpObfuscator\Contracts\IFeedback;

class Feedback implements IFeedback
{
    /**
     * Armazena mensagens emitidas no momento de gerar o código ofuscado.
     * Não são erros, mas apenas avisos de algum evento ocorrido.
     *
     * @var array<string>
     */
    private $runtime = [];

    /**
     * Armazena as mensagens de erro disparadas pelo processo de ofuscação.
     *
     * @var array<string>
     */
    protected $errors = [];

    /**
     * Controla como os erros devem ser tratados, pondendo ser armazenados na
     * pilha para posterior recuperação, ou se devem ser emitidos como exceções.
     *
     * @var bool
     */
    private $throwErrors = false;

    /**
     * Controla se o código, depois de ofuscado, pode disparar erros para o
     * usuário ou se eles devem ocorrer silenciosamente sem ser reportados
     *
     * @param  boolean $enable
     * @return IFeedback
     */
    public function enableThrowErrors($enable = true) : IFeedback
    {
        $this->throwErrors = $enable;
        return $this;
    }

    /**
     * Adiciona uma mensagem de tempo de execução.
     * Não são erros, mas apenas avisos de algum evento ocorrido.
     *
     * @param string $message
     * @return IFeedback
     */
    public function addRuntimeMessage(string $message) : IFeedback
    {
        $this->runtime[] = $message;
        return $this;
    }

    /**
     * Devolve as mensagens de tempo de execução.
     *
     * @return array<string>
     */
    public function getRuntimeMessages() : array
    {
        return $this->runtime;
    }

    /**
     * Devolve a última mensafgem de runtime ocorrida.
     *
     * @return string
     */
    public function getLastRuntimeMessage(): string
    {
        $value = array_slice($this->runtime, -1);
        return count($value) !== 0 ? $value[0] : '';
    }

    /**
     * Adiciona uma mensagem na pilha de erros.
     *
     * @param string $message
     * @return IFeedback
     */
    public function addErrorMessage(string $message) : IFeedback
    {
        if ($this->throwErrors == true) {
            throw new \RuntimeException($message);
        }

        $this->errors[] = $message;
        return $this;
    }

    /**
     * Devolve as mensagens de erro ocorridas no processo.
     *
     * @return array<string>
     */
    public function getErrorMessages() : array
    {
        return $this->errors;
    }

    /**
     * Devolve a última mensagem de erro ocorrida.
     *
     * @return string
     */
    public function getLastErrorMessage(): string
    {
        $value = array_slice($this->errors, -1);
        return count($value) !== 0 ? $value[0] : '';
    }
}
