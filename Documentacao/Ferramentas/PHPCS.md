# PHPCS

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 12/11/2026 |

## Objetivo

Este documento descreve a verificação da forma de escrita do código PHP com a ferramenta instalada em `Ferramentas/PHPCS`. As funções exigidas, a organização e o procedimento geral das ferramentas de desenvolvimento estão em [Ferramentas](../04Ferramentas.md).

## Funcionamento

O PHPCS cumpre a função de verificação da forma do código definida em [Funções exigidas](../04Ferramentas.md#funções-exigidas). Ele compara o código próprio com o conjunto de verificações configurado. Arquivos distribuídos por terceiros ficam fora do escopo de formatação.

## Instalação e configuração

O executável fica em `Ferramentas/PHPCS/PHPCS.phar`. Os dados da instalação estão em [Registro de instalação](#registro-de-instalação), com os campos definidos em [Documento de ferramenta](../01Padroes.md#documento-de-ferramenta).

O arquivo prescrito é `Ferramentas/PHPCS/PHPCS.xml`, atualmente ausente. Ele DEVE identificar as verificações adotadas, seus parâmetros e os caminhos incluídos e excluídos. A correção automática DEVE utilizar o mesmo padrão.

## Registro de instalação

| Campo | Conteúdo |
| --- | --- |
| Versão | 4.0.4; consulta por `--version` |
| Origem | [PHP_CodeSniffer 4.0.4](https://github.com/PHPCSStandards/PHP_CodeSniffer/releases/tag/4.0.4), arquivo phpcs.phar |
| Licença | [BSD-3-Clause](https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/4.0.4/licence.txt), preservada no PHAR distribuído |
| Justificativa | Pendente: registrar a justificativa de uso no projeto |
| Requisitos | PHP >=7.2.0 e extensões libxml, SimpleXML, tokenizer e XMLWriter, conforme o [composer.json da versão](https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/4.0.4/composer.json); verificações adicionais exigem os requisitos próprios declarados por cada uma |
| Executável | `Ferramentas/PHPCS/PHPCS.phar` |
| Configuração | Pendente: criar `Ferramentas/PHPCS/PHPCS.xml`, definindo as verificações e os arquivos incluídos e excluídos |
| Integridade | SHA-256: `4b010cd21d8bc8a17e2504792e3c77ef8259126f24caf7eafe7eddef1fa871e9`; fonte: https://api.github.com/repos/PHPCSStandards/PHP_CodeSniffer/releases/assets/503395408 |

## Execução

A sintaxe de invocação abaixo exige o arquivo de padrão, atualmente ausente. A execução DEVE partir da raiz do projeto; o bloco não representa uma verificação realizada:

```text
php Ferramentas/PHPCS/PHPCS.phar --standard=Ferramentas/PHPCS/PHPCS.xml
```

O arquivo de padrão DEVE definir também os caminhos próprios verificados e os recursos de terceiros excluídos da formatação.

## Resultado

O resultado informa arquivo, posição e verificação não atendida. A aprovação de forma não comprova o comportamento da aplicação.

A aprovação exige padrão e escopo identificados e atendimento aos critérios de [Configuração das verificações](../04Ferramentas.md#configuração-das-verificações). O padrão do projeto permanece pendente; é PROIBIDO considerar a execução com um padrão implícito como conferência das convenções próprias.

Atualizações e revisão de correções seguem [Atualização de ferramentas e dependências](../00Regras.md#atualização-de-ferramentas-e-dependências).
