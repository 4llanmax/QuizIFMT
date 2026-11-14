# PHPStan

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 12/11/2026 |

## Objetivo

Este documento descreve a análise estática do código PHP com a ferramenta instalada em `Ferramentas/PHPStan`. As funções exigidas, a organização e o procedimento geral das ferramentas de desenvolvimento estão em [Ferramentas](../04Ferramentas.md).

## Funcionamento

O PHPStan cumpre a função de análise estática definida em [Funções exigidas](../04Ferramentas.md#funções-exigidas). Ele verifica tipos, chamadas e contratos sem executar os casos de uso da aplicação.

## Instalação e configuração

O executável fica em `Ferramentas/PHPStan/PHPStan.phar`. Os dados da instalação estão em [Registro de instalação](#registro-de-instalação), com os campos definidos em [Documento de ferramenta](../01Padroes.md#documento-de-ferramenta).

O arquivo prescrito é `Ferramentas/PHPStan/PHPStan.neon`, atualmente ausente. A configuração DEVE identificar o nível de análise, os caminhos de código próprio e o carregamento dos símbolos utilizados. É PERMITIDO informar símbolos de dependências externas sem analisar seus arquivos como código próprio.

## Registro de instalação

| Campo | Conteúdo |
| --- | --- |
| Versão | 2.2.6; consulta por `--version` |
| Origem | [Lançamento 2.2.6](https://github.com/phpstan/phpstan/releases/tag/2.2.6), arquivo phpstan.phar distribuído pelo projeto PHPStan |
| Licença | [MIT](https://github.com/phpstan/phpstan/blob/2.2.6/LICENSE), conforme a versão publicada |
| Justificativa | Pendente: registrar a justificativa de uso no projeto |
| Requisitos | PHP 7.4 ou 8.x, conforme o [composer.json da versão](https://github.com/phpstan/phpstan/blob/2.2.6/composer.json); configuração e recursos exigidos pelas extensões utilizadas |
| Executável | `Ferramentas/PHPStan/PHPStan.phar` |
| Configuração | Pendente: criar `Ferramentas/PHPStan/PHPStan.neon`, definindo o nível, o escopo e o carregamento de símbolos |
| Integridade | SHA-256: `abe4d3b38db5c4139e0135d98ea583e65185344050812651d1e9ede56d354e20`; fonte: https://api.github.com/repos/phpstan/phpstan/releases/assets/490705810 |

## Execução

A sintaxe de invocação abaixo exige a configuração, atualmente ausente. A execução DEVE partir da raiz do projeto; o bloco não representa uma análise realizada:

```text
php Ferramentas/PHPStan/PHPStan.phar analyse --configuration Ferramentas/PHPStan/PHPStan.neon
```

O nível de análise DEVE ser definido nesse arquivo de configuração. Símbolos carregados de terceiros não alteram o destino nem a responsabilidade do código próprio.

## Resultado

O resultado identifica problemas de tipos, chamadas ou configuração. Ele não substitui testes de comportamento ou de implantação.

A aprovação exige configuração e nível identificados, execução sobre todo o escopo próprio declarado e atendimento aos critérios de [Configuração das verificações](../04Ferramentas.md#configuração-das-verificações). Sem o arquivo de configuração, a análise do projeto permanece não verificada; a conferência da versão do executável não altera essa condição.

Atualizações e revisão de correções seguem [Atualização de ferramentas e dependências](../00Regras.md#atualização-de-ferramentas-e-dependências).
