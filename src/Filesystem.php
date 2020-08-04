<?php
declare(strict_types=1);

namespace PhpObfuscator;

use League\Flysystem\Adapter;
use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\FilesystemInterface;
use RuntimeException;

class Filesystem extends LeagueFilesystem implements FilesystemInterface
{
    /**
     * Constrói um gerenciador de arquivos no diretório especificado.
     * 
     * @param string $realPath
     */
    public static function instance(string $realPath): Filesystem
    {
        $adapter = new Adapter\Local($realPath);
        return new Filesystem($adapter);
    }

    /**
     * Obtém o nome + extensão de um arquivo especificado.
     * Ex: /dir/meu-arquivo.md -> meu-arquivo.md
     */
    public static function basename($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return preg_replace('/^.+[\\\\\\/]/', '', $filename);
    }

    /**
     * Obtém o nome de um arquivo especificado.
     * Ex: /dir/meu-arquivo.md -> meu-arquivo
     */
    public static function filename($filename)
    {
        $basename = self::basename($filename);
        return preg_replace('/\\.[^.\\s]{2,}$/', '', $basename);
    }

    /**
     * Obtém o nome de um diretório com base no caminho especificado.
     * Ex: /dir/meu-arquivo.md -> /dir
     */
    public static function dirname($filename)
    {
        $basename = self::basename($filename);
        return rtrim(str_replace("/" . $basename, '', $filename), '/');
    }

    /**
     * Obtém o caminho absoluto do caminho relativo informado.
     * @see https://www.php.net/manual/en/function.realpath.php
     */
    public function absolutePath($path)
    {
        if(DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        }
        $search = explode('/', $path);
        $search = array_filter($search, function($part) {
            return $part !== '.';
        });
        $append = array();
        $match = false;
        while(count($search) > 0) {
            $match = realpath(implode('/', $search));
            if($match !== false) {
                break;
            }
            array_unshift($append, array_pop($search));
        };
        if($match === false) {
            $match = getcwd();
        }
        if(count($append) > 0) {
            $match .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $append);
        }
        return $match;
    }

    public function isFile(string $filename): bool
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return is_file($filename);
    }

    public function isDirectory(string $path): bool
    {
        $path = filter_var($path, FILTER_SANITIZE_STRING);
        return is_dir($path);
    }

    public function isDirectoryOrException($path)
    {
        if ($this->isDirectory($path) === false) {
            throw new RuntimeException("The path {$path} does not exist or is not valid");
        }

        return true;
    }
}