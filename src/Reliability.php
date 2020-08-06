<?php 
declare(strict_types=1);

namespace PhpObfuscator;

use League\Flysystem\Filesystem;
use League\Flysystem\Util;
use League\Flysystem\Adapter;
use LogicException;

/**
 * Esta cladse contém funções críticas do PHP 
 * para centralização e implementadas com uma
 * abordagem mais segura.
 */
class Reliability
{
    /**
     * Codifica uma string em base64.
     * 
     * @param string $data
     * @return string
     */
    public function encodeBase64(string $data): string
    {
        return base64_encode($data);
    }

    /**
     * Decodifica uma string do base64.
     * 
     * @return string
     */
    public function decodeBase64(string $data): string
    {
        return base64_decode($data);
    }

    /**
     * Obtém o nome + extensão de um arquivo especificado.
     * Ex: /dir/meu-arquivo.md -> meu-arquivo.md
     * 
     * @param string $filename
     * @return string
     */
    public function basename(string $filename): string
    {
        $filename = $this->removeInvalidWhiteSpaces($filename);
        return Util::pathinfo($filename)['basename'];
    }

    /**
     * Obtém o nome de um arquivo especificado.
     * Ex: /dir/meu-arquivo.md -> meu-arquivo
     * 
     * @param string $filename
     * @return string
     */
    public function filename(string $filename): string
    {
        $filename = $this->removeInvalidWhiteSpaces($filename);
        return Util::pathinfo($filename)['filename'];
    }

    /**
     * Obtém o nome de um diretório com base no caminho especificado.
     * Ex: /dir/meu-arquivo.md -> /dir
     * 
     * @param string $filenameOrDir
     * @return string
     */
    public function dirname(string $filenameOrDir): string
    {
        $filenameOrDir = $this->removeInvalidWhiteSpaces($filenameOrDir);
        return Util::pathinfo($filenameOrDir)['dirname'];
    }

    /**
     * Verifica se o caminho especificado existe e é um diretório.
     * 
     * @param string $path
     * @return bool
     */
    public function isDirectory(string $path): bool
    {
        $info = Util::pathinfo($path);
        return $this->pathExists($path) && !isset($info['extension']);
    }

    /**
     * Verifica se o caminho especificado existe e é um arquivo.
     * 
     * @param string $filename
     * @return bool
     */
    public function isFile(string $filename): bool
    {
        $info = Util::pathinfo($filename);
        return $this->pathExists($filename) && isset($info['extension']);
    }

    /**
     * Remove comentários e espaços desnecessários em um script PHP.
     * 
     * @param string $file
     * @return string
     */
    public function readFileWithoutCommentsAndWhiteSpaces(string $file): string
    {
        return php_strip_whitespace($file);
    }

    /**
     * Devolve todas as linhas de um arquivo em forma de array
     * 
     * @param string $file
     * @return array<string>
     */
    public function readFileLines(string $file): array
    {
        $directory  = $this->dirname($file);
        $basename   = $this->basename($file);

        $filesystem = $this->mountDirectory($directory);
        if ($filesystem === null) {
            return [];
        }

        $contents = (string)$filesystem->read($basename);

        $lines = explode("\n", $contents);

        // Se o array for vazio, devolve true
        if (!array_filter($lines)) {
            return [];
        }

        return $lines;
    }

    /**
     * Devolce uma instância do Filesystem apontando para o 
     * diretório especificado.
     * 
     * @param string $path
     * @return Filesystem|null
     */
    public function mountDirectory(string $path): ?Filesystem
    {
        try {
            $adapter = new Adapter\Local($path);
        } catch (LogicException $e) { 
            return null;
        }

        return new Filesystem($adapter);
    }
    
    /**
     * Verifica se o caminho especificado existe.
     * Pode ser um diretório ou um arquivo
     * 
     * @param string $path
     * @return bool
     */
    private function pathExists(string $path): bool
    {
        $path = $this->removeInvalidWhiteSpaces($path);
        return file_exists($path);
    }

    /**
     * Remove caracteres não imprimíveis e caracteres unicode inválidos.
     * 
     * @param string $path
     * @return string
     * @see vendor/league/flysystem/src/Util.php
     */
    private function removeInvalidWhiteSpaces(string $path): string
    {
        $path = (string)filter_var($path, FILTER_SANITIZE_STRING);

        while (preg_match('#\p{C}+|^\./#u', $path)) {
            $path = (string)preg_replace('#\p{C}+|^\./#u', '', $path);
        }

        return $path;
    }
}
