# 3. Como Usar

É muito fácil usar o Php Obfuscator. 

Para ofuscar um único arquivo PHP, usa-se a classe Obfuscate,
fornecendo o caminho completo até o arquivo PHP a ser processado e também a localização do resultado ofuscado.

* O método **obfuscateFile()** marca o arquivo para ofuscação;
* O método **save()** ofusca efetivamente, salvando na localização especificada.

```php
$ob = new PhpObfuscator\Obfuscate();
$ob->from('/var/www/app/projeto/arquivo.php')
   ->generate('/var/www/app/projeto/ofuscado.php');
```

```php
/*
 * Arquivo /var/www/app/index.php
 */

include 'projeto/ofuscado.php';
```

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
