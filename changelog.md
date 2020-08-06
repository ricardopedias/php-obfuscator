# Changelog

Este é o registro contendo as alterações mais relevantes efetuadas no projeto
seguindo o padrão que pode ser encontrado em [Keep a Changelog](https://keepachangelog.com/en/1.0.0).

Para obter o diff para uma versão específica, siga para o final deste documento 
e acesse a URL da versão desejada. Por exemplo, v4.0.0 ... v4.0.1.
As versões seguem as regras do [Semantic Versioning](https://semver.org/lang/pt-BR).

## \[Unreleased]

Nada implementado ainda.

## \[2.1.0] - 2020-08-06

### Added

-   Badges no arquivo readme.md
-   Arquivo changelog.md
-   Biblioteca phpstan para análise do código
-   Classe Reliability, para implementação de funções inseguras do PHP
-   Pipeline para o Travis CI

### Fixed 

-   Code smells detectados pelo Codacy

### Removed

-   Classe Filesystem foi inutilizada por conta das novas implementações e foi removida

## \[2.0.0] - 2020-08-04

### Added

-   Atualização no arquivo readme.md
-   Atualização no arquivo license.md
-   Desmembradas as classes para prover maior manutenibilidade
-   Criação de inúmeros testes de unidade para suportar o novo design do código

### Changed

-   Diversas melhorias no código fonte e adição de novos testes de unidade

### Removed

-   Testes de unidade obsoletos por motivo da refatoração

## \[1.2.0] - 2018-08-10

### Added

-   Implementado método para impedimento ofuscação de diretórios já ofuscados

## \[1.1.0] - 2018-08-09

### Changed

-   Visualização pública para o método de criação de diretórios 

## \[1.0.0] - 2018-08-08

### Added

-   Documentação de uso da biblioteca.
-   Adicionada fluência dos setters em vários testes de unidade.
-   Otimização e melhorias nas funções desempacotadoras

### Fixed

-   Ajustes nas ferramentas para a criação de diretórios de teste.

## \[0.0.2] - 2018-08-07

### Changed

-   Refatoração dos testes de unidade.

## \[0.0.1] - 2018-08-06

### Added

-   Criação da estrutura básica do projeto.

## Releases

-   Unreleased <https://github.com/ricardopedias/php-obfuscator/compare/v2.1.0...HEAD>
-   2.1.0 <https://github.com/ricardopedias/php-obfuscator/compare/v2.0.0...v2.1.0>
-   2.0.0 <https://github.com/ricardopedias/php-obfuscator/compare/v1.2.0...v2.0.0>
-   1.2.0 <https://github.com/ricardopedias/php-obfuscator/compare/v1.1.0...v1.2.0>
-   1.1.0 <https://github.com/ricardopedias/php-obfuscator/compare/v1.0.0...v1.1.0>
-   1.0.0 <https://github.com/ricardopedias/php-obfuscator/compare/v0.0.2...v1.0.0>
-   0.0.2 <https://github.com/ricardopedias/php-obfuscator/compare/v0.0.1...v0.0.2>
-   0.0.1 <https://github.com/ricardopedias/php-obfuscator/releases/tag/v0.0.0>
