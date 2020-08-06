<?php 
declare(strict_types=1);

namespace PhpObfuscator\Contracts;

interface IFeedback
{
    /**
     * Controla se o código, depois de ofuscado, pode disparar erros para o
     * usuário ou se eles devem ocorrer silenciosamente sem ser reportados
     *
     * @param  boolean $enable
     * @return IFeedback
     */
    public function enableThrowErrors($enable = true) : IFeedback;

    /**
     * Adiciona uma mensagem de tempo de execução.
     * Não são erros, mas apenas avisos de algum evento ocorrido.
     *
     * @param string $message
     * @return IFeedback
     */
    public function addRuntimeMessage(string $message) : IFeedback;

    /**
     * Devolve as mensagens de tempo de execução.
     *
     * @return array<string>
     */
    public function getRuntimeMessages() : array;

    /**
     * Devolve a última mensafgem de runtime ocorrida.
     *
     * @return string
     */
    public function getLastRuntimeMessage(): string;

    /**
     * Adiciona uma mensagem na pilha de erros.
     *
     * @param string $message
     * @return \PhpObfuscator\Feedback
     */
    public function addErrorMessage(string $message) : IFeedback;

    /**
     * Devolve as mensagens de erro ocorridas no processo.
     *
     * @return array<string>
     */
    public function getErrorMessages() : array;

    /**
     * Devolve a última mensagem de erro ocorrida.
     *
     * @return string
     */
    public function getLastErrorMessage(): string;
}
